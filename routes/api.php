<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankPledgeController;
use App\Http\Controllers\PledgesController;

// API Routes

//Auth Routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

//Customers Routes
Route::middleware(['auth:api'])->group(function () {
    Route::get('/customers', [CustomerController::class, 'getAll']);
    Route::post('/customers', [CustomerController::class, 'createData']);
    Route::put('/customers/{uuid}', [CustomerController::class, 'updateData']);
    Route::delete('/customers/{id}', [CustomerController::class, 'DeleteCustomer']);
    Route::get('/customers/{uuid}', [CustomerController::class, 'getCustomerByUuid']);
});

//Pledges Routes
Route::middleware(['auth:api'])->group(function () {
    Route::get('/pledges', [PledgesController::class, 'getAll']);
    Route::post('/pledges', [PledgesController::class, 'createData']);
    Route::delete('/pledges/{id}', [PledgesController::class, 'deleteData']);
    Route::get('/pledges/{hashid}', [PledgesController::class, 'getPledgeById']);
});

//Bank Pledge Routes
Route::middleware(['auth:api'])->group(function () {
    Route::get('/bank-pledges/{loanId}', [BankPledgeController::class, 'getLoanDetails']);
});
