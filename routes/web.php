<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/items/{item:slug}/add-to-cart', CartController::class)->name('add-to-cart');
