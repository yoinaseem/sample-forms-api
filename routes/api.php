<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\FormSectionController;
use App\Http\Controllers\FormFieldController;
use App\Http\Controllers\Api\SubmissionController;
use App\Http\Controllers\Api\SubmissionValueController;
use Illuminate\Support\Facades\Log; 

Route::post('/login', [AuthController::class, 'login']);

Route::apiResource('forms', FormController::class);

Route::post('forms/{form}/sections', [FormSectionController::class, 'store']);
Route::apiResource('form-sections', FormSectionController::class)->except(['index', 'store']);

Route::post('form-sections/{formSection}/fields', [FormFieldController::class, 'store']);
Route::apiResource('form-fields', FormFieldController::class)->except(['index', 'store']);

// Submission routes
Route::get('/submissions', [SubmissionController::class, 'index']);
Route::post('/submissions', [SubmissionController::class, 'store']);
Route::get('/submissions/{id}', [SubmissionController::class, 'show']);
Route::put('/submissions/{id}', [SubmissionController::class, 'update']);
Route::delete('/submissions/{id}', [SubmissionController::class, 'destroy']);

// SubmissionValue routes
Route::get('/submission-values', [SubmissionValueController::class, 'index']);
Route::get('/submission-values/{id}', [SubmissionValueController::class, 'show']);
Route::put('/submission-values/{id}', [SubmissionValueController::class, 'update']);
Route::delete('/submission-values/{id}', [SubmissionValueController::class, 'destroy']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('users', UserController::class);
});