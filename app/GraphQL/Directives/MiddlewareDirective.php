<?php
namespace App\GraphQL\Directives;

use Closure;
use GraphQL\Language\AST\FieldDefinitionNode;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\Directive;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
// use Nuwave\Lighthouse\Schema\Directives\Directive;

class MiddlewareDirective extends BaseDirective
{
    public function name(): string
    {
        return 'middleware';
    }

    // public function handleField(): FieldDefinitionNode
    // {
    //     $middleware = $this->directiveArgValue('checks'); // Get the specified middleware from the directive

    //     // Apply the middleware to the field resolver
    //     $this->fieldResolver->middleware($middleware);

    //     return $this->fieldDefinition;
    // }

    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
            """
            Log the GraphQL query and variables.
            """
            directive @subDomain on FIELD_DEFINITION
        GRAPHQL;
    }

    public function handleField(FieldValue $fieldValue, Closure $next): FieldValue
    {
        // Apply the middleware to the field resolver
        return $next($fieldValue)->middleware([
            PreventAccessFromCentralDomains::class,
            InitializeTenancyByDomain::class,
        ]);
        }
}