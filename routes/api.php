<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Translation\TranslationController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('translations/tags/{tagName}', [TranslationController::class, 'getTranslationsByTag']);
    Route::get('translations/search', [TranslationController::class, 'search']);
    Route::post('translations/{translation}/assign-tags', [TranslationController::class, 'assignTags']);
    Route::get('translations/export', [TranslationController::class, 'export']);
    Route::apiResource('translations', TranslationController::class);
});