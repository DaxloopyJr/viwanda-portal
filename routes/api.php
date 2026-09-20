<?php

use App\Http\Controllers\Api\V1\DatasetController;
use App\Http\Controllers\Api\V1\SubmissionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::get('ping', fn () => response()->json(['status' => 'ok', 'time' => now()->toIso8601String()]));
    Route::post('submissions', [SubmissionController::class, 'store']);
    Route::get('submissions/{reference}', [SubmissionController::class, 'show']);
    Route::get('datasets/{code}/schema', [DatasetController::class, 'schema']);
});
