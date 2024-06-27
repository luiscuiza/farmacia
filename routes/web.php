<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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


});