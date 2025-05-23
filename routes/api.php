<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->name('v1.')->middleware(['force-json'])->group(function () {

    Route::prefix('subscriber')->name('subscriber.')->group(function () {
        Route::put('/', [App\Http\Controllers\Api\v1\SubscriberController::class, 'create'])->name('register');
        Route::post('/', [App\Http\Controllers\Api\v1\SubscriberController::class, 'login'])->name('login');
    });

    Route::prefix('usage')->name('usage.')->middleware(['auth:sanctum'])->group(function () {
        Route::put('/', [App\Http\Controllers\Api\v1\UsageController::class, 'addUsage'])->name('add-usage');
    });

    Route::prefix('bill')->name('bill.')->group(function () {
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::post('calculate', [App\Http\Controllers\Api\v1\BillController::class, 'calculateBill'])->name('calculate');
            Route::post('query-detailed', [App\Http\Controllers\Api\v1\BillController::class, 'queryBillDetailed'])->name('query-detailed');
            Route::post('pay', [App\Http\Controllers\Api\v1\BillController::class, 'makePayment'])->name('pay-bill');
            Route::post('query', [App\Http\Controllers\Api\v1\BillController::class, 'queryBill'])->name('query');
        });
    });
});
