<?php

use App\Http\Controllers\userController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Users
Route::get('/users', [userController::class, "selectUsers"])->middleware('login');

//user
Route::post('/user', [userController::class, "addUser"]);
Route::get('/user', [userController::class, "selectuser"])->middleware('login');
Route::patch('/user', [userController::class, "updateUser"])->middleware('login');
Route::delete('/user', [userController::class, "deleteUser"])->middleware('login');

//login
Route::get('/login', [userController::class, "login"]);
