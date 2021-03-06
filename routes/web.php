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
        'prefix' => 'buyer',
        'middleware' => 'role',
        'roles' => ['administrator', 'buyer']
    ], function () {
        // Buyer
        Route::post('product/exists', [\App\Http\Controllers\ProductController::class, 'exists']);
        Route::resource('product', \App\Http\Controllers\ProductController::class);
        Route::resource('brand', \App\Http\Controllers\BrandController::class);
        Route::resource('purchase', \App\Http\Controllers\PurchaseController::class);
        Route::resource('product-price', \App\Http\Controllers\ProductPriceController::class);
        Route::get('purchase-request', [\App\Http\Controllers\PurchaseRequestController::class, 'index'])->name('buyer.purchase-request.index');
        Route::get('purchase-request/{purchase_request}/edit', [\App\Http\Controllers\PurchaseRequestController::class, 'edit'])->name('buyer.purchase-request.edit');
        Route::post('purchase-request/{purchase_request}/mark-excel-downloaded', [\App\Http\Controllers\PurchaseRequestController::class, 'excelDownloaded']);
        Route::get('inventory', [\App\Http\Controllers\InventoryController::class, 'index'])->name('buyer.inventory.index');
        Route::get('inventory/distribution', [\App\Http\Controllers\InventoryController::class, 'distribution'])->name('buyer.inventory.distribution');
        Route::post('inventory/distribution', [\App\Http\Controllers\InventoryController::class, 'storeDistribution']);
        Route::post('inventory/not-found', [\App\Http\Controllers\InventoryController::class, 'markAsNotFound']);
        Route::resource('supplier', \App\Http\Controllers\SupplierController::class);
        Route::get('report/product', [\App\Http\Controllers\ReportController::class, 'product'])->name('report.product');
        Route::post('report/product', [\App\Http\Controllers\ReportController::class, 'productData']);
        Route::get('report/order', [\App\Http\Controllers\ReportController::class, 'order'])->name('report.order');
        Route::post('report/order', [\App\Http\Controllers\ReportController::class, 'orderData']);
        Route::get('download/product', [\App\Http\Controllers\ReportController::class, 'productDownload'])->name('download.product');
        Route::get('report/comparative', [\App\Http\Controllers\ReportController::class, 'comparative'])->name('report.comparative');
        Route::post('report/comparative', [\App\Http\Controllers\ReportController::class, 'comparativeData']);
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
