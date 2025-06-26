<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http; // Memastikan Http facade diimpor

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});


