<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});


// Route::post('/register', [AuthContoller::class, 'register']);
// Route::post('/login', [AuthContoller::class, 'login']);
// Route::post('/logout', [AuthContoller::class, 'logout']);
