<?php

use Illuminate\Support\Facades\Route;

Route::get('/sanctum/csrf-cookie', function () {
    return response()->noContent();
});

Route::get('/', function () {
    return view('welcome');
});
