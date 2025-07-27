<?php

use App\Http\Controllers\TopicController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// My Api's :)
Route::prefix('topics')->name('topics.')->group(function () {
    Route::post('/{parentTopic?}', [TopicController::class, 'store']);
    Route::patch('/{topic}', [TopicController::class, 'update']);
});
