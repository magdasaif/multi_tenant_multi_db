<?php

declare(strict_types=1);

namespace App\Overrides\Stancl\Tenancy\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventAccessFromCentralDomains
{
    /**
     * Set this property if you want to customize the on-fail behavior.
     *
     * @var callable|null
     */
    public static $abortRequest;

    public function handle(Request $request, Closure $next)
    {
        // return ['name'=>'test from prevent'];

        if (in_array($request->getHost(), config('tenancy.central_domains'))) {
                        
            //  return ['name'=>'inside prevent if , central domain'];

            // $abortRequest = static::$abortRequest ?? function () {
            //     abort(404);
            // };

            // return $abortRequest($request, $next);
        }else{
            // return ['name'=>'inside prevent else , sub domain'];
        }

        return $next($request);
    }
}
