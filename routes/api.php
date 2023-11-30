<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttenderController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([], function() {
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
});

Route::group([
    'prefix' => 'dashboard',
    'middleware' => 'check_token',
], function() {
    Route::get('/', [DashboardController::class, 'summary'])->name('dashboard.summary');
});

Route::group([], function() {
    Route::group([
        'prefix' => 'attenders',
        'middleware' => 'check_token',
    ], function() {
        Route::get('/', [AttenderController::class, 'list'])->name('attender.list');
    });
    Route::group([
        'prefix' => 'attender',
    ], function() {
        Route::post('/', [AttenderController::class, 'create'])->name('attender.create');
        Route::get('/displayed-comments', [AttenderController::class, 'getDisplayedComment'])->name('attender.getDisplayedComment');
    });
    Route::group([
        'prefix' => 'attender',
        'middleware' => 'check_token',
    ], function() {
        Route::get('/{id}', [AttenderController::class, 'detail'])->name('attender.detail');
        Route::put('/attend', [AttenderController::class, 'attend'])->name('attender.attend');
    });
    Route::group([
        'prefix' => 'attender',
        'middleware' => ['check_token', 'only_admin'],
    ], function() {
        Route::get('/active/{id}', [AttenderController::class, 'activeStatus'])->name('attender.activeStatus');
        Route::get('/inactive/{id}', [AttenderController::class, 'inactiveStatus'])->name('attender.inactiveStatus');
        Route::delete('/{id}', [AttenderController::class, 'delete'])->name('attender.delete');
    });
});
