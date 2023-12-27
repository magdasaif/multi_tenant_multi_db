<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TenantController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Auth::routes();
Route::get('/', function () {
    return view('welcome');
});
    
Route::get('/create_tenant/{domain}/{project}', [TenantController::class, 'index']);
// Route::get('/create_user/{name}/{email}/password', [UserController::class, 'create']);

Route::get('/test_rest/{user}', [TestController::class, 'test_rest']);
Route::get('/users', [TestController::class, 'users']);
Route::get('/all_users', [TestController::class, 'allTenantUsers']);


