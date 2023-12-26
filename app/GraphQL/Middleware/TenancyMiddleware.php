<?php

namespace App\GraphQL\Middleware;

use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\FieldMiddleware;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final class TenancyMiddleware extends FieldMiddleware
{
    public static function definition(): string {}

    /**
     * Wrap around the final field resolver.
     */
    public function handleField(FieldValue $fieldValue): void
    {
        $fieldValue->wrapResolver(fn (callable $resolver) => function (mixed $root, array $args, GraphQLContext $context, ResolveInfo $info) use ($resolver): string {
            // Call the resolver, passing along the resolver arguments
            $result = $resolver($root, $args, $context, $info);
            assert(is_string($result));

            return strtoupper($result);
        });
    }
}