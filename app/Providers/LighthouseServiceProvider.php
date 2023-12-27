<?php

namespace Nuwave\Lighthouse;

use GraphQL\Error\Error;
use GraphQL\Error\ClientAware;
use Illuminate\Routing\Router;
use Illuminate\Http\JsonResponse;
use GraphQL\Executor\ExecutionResult;
use Illuminate\Support\ServiceProvider;
use Nuwave\Lighthouse\Support\AppVersion;
use Nuwave\Lighthouse\Execution\ErrorPool;
use Nuwave\Lighthouse\Schema\TypeRegistry;
use Nuwave\Lighthouse\Console\CacheCommand;
use Nuwave\Lighthouse\Console\QueryCommand;
use Nuwave\Lighthouse\Console\UnionCommand;
use Nuwave\Lighthouse\Schema\SchemaBuilder;
use Nuwave\Lighthouse\Console\ScalarCommand;
use Nuwave\Lighthouse\Schema\AST\ASTBuilder;
use Illuminate\Contracts\Container\Container;
use Nuwave\Lighthouse\Console\MutationCommand;
use Nuwave\Lighthouse\Schema\DirectiveLocator;
use Nuwave\Lighthouse\Schema\ResolverProvider;
use Nuwave\Lighthouse\Console\DirectiveCommand;
use Nuwave\Lighthouse\Console\IdeHelperCommand;
use Nuwave\Lighthouse\Console\InterfaceCommand;
use Nuwave\Lighthouse\Console\ValidatorCommand;
use Nuwave\Lighthouse\Execution\ContextFactory;
use Nuwave\Lighthouse\Execution\SingleResponse;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Console\ClearCacheCommand;
use Nuwave\Lighthouse\Console\PrintSchemaCommand;
use Laravel\Lumen\Application as LumenApplication;
use Nuwave\Lighthouse\Console\SubscriptionCommand;
use Nuwave\Lighthouse\Schema\Source\SchemaStitcher;
use Nuwave\Lighthouse\Console\ValidateSchemaCommand;
use Nuwave\Lighthouse\Testing\TestingServiceProvider;
use Nuwave\Lighthouse\ClientDirectives\ClientDirective;
use Nuwave\Lighthouse\Support\Contracts\CreatesContext;
use Nuwave\Lighthouse\Execution\ValidationRulesProvider;
use Nuwave\Lighthouse\Support\Contracts\CreatesResponse;
use Nuwave\Lighthouse\Exceptions\RendersErrorsExtensions;
use Nuwave\Lighthouse\Schema\Source\SchemaSourceProvider;
use Nuwave\Lighthouse\Support\Contracts\ProvidesResolver;
use Nuwave\Lighthouse\Support\Contracts\CanStreamResponse;
use Illuminate\Foundation\Application as LaravelApplication;
use Nuwave\Lighthouse\Support\Http\Responses\ResponseStream;
use Nuwave\Lighthouse\Support\Compatibility\MiddlewareAdapter;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Nuwave\Lighthouse\Support\Contracts\ProvidesValidationRules;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Nuwave\Lighthouse\Support\Compatibility\LumenMiddlewareAdapter;
use Nuwave\Lighthouse\Support\Compatibility\LaravelMiddlewareAdapter;
use Nuwave\Lighthouse\Support\Contracts\ProvidesSubscriptionResolver;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;

class LighthouseServiceProvider extends ServiceProvider
{
    /**
     * @var array<int, class-string<\Illuminate\Console\Command>>
     */
    public const COMMANDS = [
        CacheCommand::class,
        ClearCacheCommand::class,
        DirectiveCommand::class,
        IdeHelperCommand::class,
        InterfaceCommand::class,
        MutationCommand::class,
        PrintSchemaCommand::class,
        QueryCommand::class,
        ScalarCommand::class,
        SubscriptionCommand::class,
        UnionCommand::class,
        ValidateSchemaCommand::class,
        ValidatorCommand::class,
    ];

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/lighthouse.php', 'lighthouse');

        $this->app->singleton(GraphQL::class);
        $this->app->singleton(ASTBuilder::class);
        $this->app->singleton(SchemaBuilder::class);
        $this->app->singleton(DirectiveLocator::class);
        $this->app->singleton(TypeRegistry::class);
        $this->app->singleton(ErrorPool::class);
        $this->app->singleton(CreatesContext::class, ContextFactory::class);
        $this->app->singleton(CanStreamResponse::class, ResponseStream::class);

        $this->app->bind(CreatesResponse::class, SingleResponse::class);

        $this->app->singleton(SchemaSourceProvider::class, static function (): SchemaStitcher {
            return new SchemaStitcher(
                config('lighthouse.schema.register', '')
            );
        });

        $this->app->bind(ProvidesResolver::class, ResolverProvider::class);
        $this->app->bind(ProvidesSubscriptionResolver::class, static function (): ProvidesSubscriptionResolver {
            return new class() implements ProvidesSubscriptionResolver {
                public function provideSubscriptionResolver(FieldValue $fieldValue): \Closure
                {
                    throw new \Exception(
                        'Add the SubscriptionServiceProvider to your config/app.php to enable subscriptions.'
                    );
                }
            };
        });

        $this->app->bind(ProvidesValidationRules::class, ValidationRulesProvider::class);

        $this->app->singleton(MiddlewareAdapter::class, static function (Container $app): MiddlewareAdapter {
            // prefer using fully-qualified class names here when referring to Laravel-only or Lumen-only classes
            if ($app instanceof LaravelApplication) {
                return new LaravelMiddlewareAdapter(
                    $app->get(Router::class)
                );
            }

            if ($app instanceof LumenApplication) {
                return new LumenMiddlewareAdapter();
            }

            throw new \Exception(
                'Could not correctly determine Laravel framework flavor, got ' . get_class($app) . '.'
            );
        });

        $this->commands(self::COMMANDS);

        // Always registered in order to ensure macros are recognized by Larastan
        $this->app->register(TestingServiceProvider::class);

        $this->app->register(\Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class);

        // Register your custom middleware here
        // app('router')->aliasMiddleware('test1', \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class);
        // app('router')->aliasMiddleware('test2', \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class);
        
        // app(ClientDirective::class)->registerMany([
        //     'middleware' => \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
        // ]);

    }

    public function boot(ConfigRepository $configRepository): void
    {
        $this->publishes([
            __DIR__ . '/lighthouse.php' => $this->app->configPath() . '/lighthouse.php',
        ], 'lighthouse-config');

        $this->publishes([
            __DIR__ . '/default-schema.graphql' => $configRepository->get('lighthouse.schema.register'),
        ], 'lighthouse-schema');

        $this->loadRoutesFrom(__DIR__ . '/Support/Http/routes.php');

        $exceptionHandler = $this->app->make(ExceptionHandlerContract::class);
        if (
            $exceptionHandler instanceof ExceptionHandler
            // TODO remove when requiring a later Laravel version
            && method_exists($exceptionHandler, 'renderable')
        ) {
            $exceptionHandler->renderable(
                function (ClientAware $error) {
                    assert($error instanceof \Throwable);

                    if (! $error instanceof Error) {
                        $error = new Error(
                            $error->getMessage(),
                            null,
                            null,
                            [],
                            null,
                            $error,
                            $error instanceof RendersErrorsExtensions ? $error->extensionsContent() : []
                        );
                    }

                    $graphQL = $this->app->make(GraphQL::class);
                    assert($graphQL instanceof GraphQL);

                    $executionResult = new ExecutionResult(null, [$error]);

                    return new JsonResponse($graphQL->serializable($executionResult));
                }
            );
        }
    }

    protected function loadRoutesFrom($path): void
    {
        if (AppVersion::isLumen()) {
            require \Safe\realpath($path);

            return;
        }

        parent::loadRoutesFrom($path);
    }
}