<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->name('v1.')->middleware('force-json')->group(function() {

    Route::prefix('subscriber')->name('subscriber.')->group(function() {
        Route::put('/', [App\Http\Controllers\Api\v1\SubscriberController::class, 'create']);
    });


});

