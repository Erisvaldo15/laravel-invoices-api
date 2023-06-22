<?php

use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\AuthController;
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

Route::apiResources([
    'users' => UserController::class,
    'invoices' => InvoiceController::class,
]);

Route::controller(AuthController::class)->group(function () {
    Route::post("/auth/login", 'login');
    Route::post("/auth/logout", 'logout');
});
