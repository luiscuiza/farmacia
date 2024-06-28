<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LaboratoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DashboardController;

/* Redirect 404 */
Route::fallback(function () {
    return redirect('/');
});


Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    } else {
        return redirect('/login');
    }
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::controller(LaboratoryController::class)->group(function () {
        Route::get('/laboratories', 'index')->name('laboratories.index');
        Route::get('/laboratories/{id}', 'info')->name('laboratories.info');
        Route::post('/laboratories/store', 'store')->name('laboratories.store');
        Route::post('/laboratories/update/{id}', 'update')->name('laboratories.update');
        Route::delete('/laboratories/destroy/{id}', 'destroy')->name('laboratories.destroy');
    });
    
    Route::controller(ProductController::class)->group(function () {
        Route::get('/products', 'index')->name('products.index');
        Route::get('/products/{id}', 'info')->name('products.info');
        Route::post('/products/store', 'store')->name('products.store');
        Route::post('/products/update/{id}', 'update')->name('products.update');
        Route::delete('/products/destroy/{id}', 'destroy')->name('products.destroy');
    });
    
    Route::controller(BatchController::class)->group(function () {
        Route::get('/batches', 'index')->name('batches.index');
        Route::get('/batches/{id}', 'info')->name('batches.info');
        Route::post('/batches/store', 'store')->name('batches.store');
        Route::post('/batches/update/{id}', 'update')->name('batches.update');
        Route::delete('/batches/destroy/{id}', 'destroy')->name('batches.destroy');
    });

    Route::controller(SupplierController::class)->group(function () {
        Route::get('/suppliers', 'index')->name('suppliers.index');
        Route::get('/suppliers/{id}', 'info')->name('suppliers.info');
        Route::post('/suppliers/store', 'store')->name('suppliers.store');
        Route::post('/suppliers/update/{id}', 'update')->name('suppliers.update');
        Route::delete('/suppliers/destroy/{id}', 'destroy')->name('suppliers.destroy');
    });

    Route::controller(CustomerController::class)->group(function () {
        Route::get('/customers', 'index')->name('customers.index');
        Route::get('/customers/{id}', 'show')->name('customers.show');
        Route::post('/customers/store', 'store')->name('customers.store');
        Route::post('/customers/update/{id}', 'update')->name('customers.update');
        Route::delete('/customers/destroy/{id}', 'destroy')->name('customers.destroy');
    });

    Route::controller(SaleController::class)->group(function () {
        Route::get('/sales', 'index')->name('sales.index');
        Route::get('/sales/{id}', 'info')->name('sales.info');
        Route::post('/sales/store', 'store')->name('sales.store');
        Route::post('/sales/update/{id}', 'update')->name('sales.update');
        Route::delete('/sales/destroy/{id}', 'destroy')->name('sales.destroy');
    });

    

});