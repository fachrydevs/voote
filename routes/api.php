<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\ElectionController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\DashboardController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum','admin');

Route::prefix('/v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/profile', [AuthController::class, 'profile'])->middleware('auth:sanctum');
});

Route::apiResource('/elections', ElectionController::class)->middleware(['auth:sanctum','admin'])->except(['index','show']);
Route::get('/elections/active', [ElectionController::class, 'getActiveElections']);
Route::get('/elections', [ElectionController::class, 'index']);
Route::get('/elections/{election}', [ElectionController::class, 'show']);
Route::apiResource('elections.candidates', CandidateController::class)->middleware(['auth:sanctum','admin']);
Route::post('/elections/{election}/vote', [VoteController::class, 'store'])->middleware('auth:sanctum');
Route::get('/elections/{election}/vote-status', [VoteController::class,'checkStatus'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum','admin'])->group(function () {
    Route::get('/dashboard/statistics', [DashboardController::class, 'statistics']);
    Route::get('/dashboard/audit-logs', [DashboardController::class, 'auditLogs']);
    Route::get('/dashboard/recent-activity', [DashboardController::class, 'recentActivity']);
});

Route::get('/debug-url-check', function () {
    return response()->json([
        'env_app_url' => env('APP_URL'),
        'config_app_url' => config('app.url'),
        'storage_url_test' => url(Storage::url('test.jpg')),
    ]);
});
