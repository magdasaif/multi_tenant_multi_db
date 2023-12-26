<?php
namespace App\GraphQL\Directives;

use Closure;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Nuwave\Lighthouse\Support\Contracts\ArgDirectiveForArray;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

class SubDomainDirective extends BaseDirective implements ArgDirectiveForArray
{
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