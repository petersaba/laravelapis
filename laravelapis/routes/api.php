<?php

use App\Http\Controllers\GeneralController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test/api1/{data}', [GeneralController::class, 'firstApi']);

Route::get('/test/api2/{number}', [GeneralController::class, 'secondApi']);

Route::get('/test/api3/{sentence}', [GeneralController::class, 'thirdApi']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
