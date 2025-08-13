<?php

use App\Http\Controllers\authContoroller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post("/user", [authContoroller::class, "register"]);
Route::get("/user", [authContoroller::class, "login"]);

Route::middleware("authaja")->group(function () {
    Route::get('user/logout', [authContoroller::class, "logout"]);
    Route::get('user/profile', [authContoroller::class, "myProfile"]);
    Route::patch("/user/updatePassword", [authContoroller::class, "updatePassword"]);
});