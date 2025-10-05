<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});

Route::middleware(['auth:api'])->group(function () {
    Route::get('/customers', [CustomerController::class, 'getAll']);
    Route::post('/customers', [CustomerController::class, 'createData']);
    Route::put('/customers/{uuid}', [CustomerController::class, 'updateData']);
    Route::delete('/customers/{id}', [CustomerController::class, 'DeleteCustomer']);
    Route::get('/customers/{uuid}', [CustomerController::class, 'getCustomerByUuid']);
});
