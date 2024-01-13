<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RoomController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'property'], function () {
    Route::post('/', [PropertyController::class, 'store']);
    Route::get('/', [PropertyController::class, 'index']);
    Route::get('{property}', [PropertyController::class, 'show']);
    Route::put('{property}', [PropertyController::class, 'update']);
    Route::delete('{property}', [PropertyController::class, 'destroy']);
});

Route::group(['prefix' => 'room'], function () {
    Route::post('/', [RoomController::class, 'store']);
    Route::get('{property}', [RoomController::class, 'index']);
    Route::put('{room}', [RoomController::class, 'update']);
    Route::delete('{room}', [RoomController::class, 'destroy']);
});
