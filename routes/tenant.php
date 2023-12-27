<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    

    Route::get('/create_user/{name}/{email}/{password}',[UserController::class, 'create']);

    // Route::get('/create_user/{name}/{email}/{password}', function(){
    //     // [UserController::class, 'create']
    //     dd('dddd');
    // });

    Route::get('/', function () {
        dd(\App\Models\User::all());
        // dd(tenant());
        return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
    });


});
