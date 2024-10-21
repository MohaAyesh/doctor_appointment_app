<?php

use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\DocsController;
use App\Http\Controllers\UsersContorller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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
Route::post('/login', [UsersContorller::class, 'login']);
Route::post('/register', [UsersContorller::class, 'register']);

 
Route::middleware('auth:sanctum')->group(function() {
    Route::get('/user', [UsersContorller::class, 'index']);
    Route::post('/fav', [UsersContorller::class, 'storeFavDoc']);
    Route::post('/logout', [UsersContorller::class, 'logout']);
    Route::post('/book', [AppointmentsController::class, 'store']);
    Route::post('/reviews', [DocsController::class, 'store']);
    Route::get('/appointments', [AppointmentsController::class, 'index']);
    Route::post('/destroy', [AppointmentsController::class, 'destroy']);
    Route::post('/rebook', [AppointmentsController::class, 'rescheduleAppointment']);
    Route::post('/category', [UsersContorller::class, 'getCategory']);
    Route::post('/updateuser', [UsersContorller::class, 'updateUserProfile']);
    Route::post('/updateuserinfo', [UsersContorller::class, 'updateUserInfo']);
});
