<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProjectTopicController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/project-topics', [ProjectTopicController::class, 'index']);
Route::get('/project-topics/{id}', [ProjectTopicController::class, 'show']);
Route::post('/project-topics', [ProjectTopicController::class, 'store']);
Route::put('/project-topics/{id}', [ProjectTopicController::class, 'update']);
Route::delete('/project-topics/{id}', [ProjectTopicController::class, 'destroy']);