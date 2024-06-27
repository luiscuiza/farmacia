<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LaboratoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BatchController;

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

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

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
    });

});