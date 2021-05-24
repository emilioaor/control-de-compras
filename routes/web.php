<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::get('translation/{locale}', [\App\Http\Controllers\Controller::class, 'translations']);

Route::group(['middleware' => 'auth'], function () {
    // Auth users

    Route::group(['prefix' => 'admin', 'middleware' => 'role', 'roles' => ['administrator']], function () {
        // Administrator
        Route::post('user/exists', [\App\Http\Controllers\UserController::class, 'exists']);
        Route::resource('user', \App\Http\Controllers\UserController::class);
        Route::post('product/exists', [\App\Http\Controllers\ProductController::class, 'exists']);
        Route::resource('product', \App\Http\Controllers\ProductController::class);
    });

    Route::group([
        'prefix' => 'seller',
        'middleware' => 'role',
        'roles' => ['administrator', 'seller']
    ], function () {
        // Seller
        Route::resource('purchase-request', \App\Http\Controllers\PurchaseRequestController::class);
    });

    Route::group([
        'prefix' => 'buy',
        'middleware' => 'role',
        'roles' => array_keys(\App\Models\User::rolesAvailable())
    ], function () {
        // Everybody
        Route::get('user/config', [\App\Http\Controllers\UserController::class, 'config'])->name('user.config');
        Route::put('user/config', [\App\Http\Controllers\UserController::class, 'updateConfig'])->name('user.updateConfig');
        Route::get('product', [\App\Http\Controllers\ProductController::class, 'index']);
        Route::get('product/models', [\App\Http\Controllers\ProductController::class, 'models']);
        Route::get('product/models/{model}', [\App\Http\Controllers\ProductController::class, 'byModel']);
    });

});
