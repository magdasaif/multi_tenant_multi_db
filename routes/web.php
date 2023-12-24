<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\Auth\RegisterController;

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

Route::get('/users', function () {
    dd(User::all());
});

Route::get('/', function () {
    return view('welcome');
});
    
Route::get('/create_tenant/{domain}', [TenantController::class, 'index']);
// Route::get('/create_user/{name}/{email}/password', [UserController::class, 'create']);


