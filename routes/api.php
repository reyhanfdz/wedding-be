<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttenderController;
use App\Http\Controllers\BlockDomainController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingController;

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
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

Route::group([
    'prefix' => 'dashboard',
    'middleware' => 'check_token',
], function() {
    Route::get('/', [DashboardController::class, 'summary']);
});

Route::group([
    'middleware' => ['check_token', 'only_admin'],
], function() {
    Route::group([
        'prefix' => 'block-domains',
    ], function() {
        Route::get('/', [BlockDomainController::class, 'list']);
    });
    Route::group([
        'prefix' => 'block-domain',
    ], function() {
        Route::post('/', [BlockDomainController::class, 'create']);
        Route::delete('/{id}', [BlockDomainController::class, 'delete']);
    });
});

Route::group([], function() {
    Route::group([
        'prefix' => 'attenders',
        'middleware' => 'check_token',
    ], function() {
        Route::get('/', [AttenderController::class, 'list']);
    });
    Route::group([
        'prefix' => 'attender',
    ], function() {
        Route::post('/', [AttenderController::class, 'create']);
        Route::get('/displayed-comments', [AttenderController::class, 'getDisplayedComment']);
    });
    Route::group([
        'prefix' => 'attender',
        'middleware' => 'check_token',
    ], function() {
        Route::get('/{id}', [AttenderController::class, 'detail']);
        Route::put('/attend', [AttenderController::class, 'attend']);
    });
    Route::group([
        'prefix' => 'attender',
        'middleware' => ['check_token', 'only_admin'],
    ], function() {
        Route::get('/active/{id}', [AttenderController::class, 'activeStatus']);
        Route::get('/regenerate-qr/{id}', [AttenderController::class, 'generateNewQr']);
        Route::get('/inactive/{id}', [AttenderController::class, 'inactiveStatus']);
        Route::delete('/{id}', [AttenderController::class, 'delete']);
    });
});

Route::group([
    'prefix' => 'setting',
], function() {
    Route::group([
        'middleware' => ['check_token', 'only_admin'],
    ], function() {
        Route::post('/', [SettingController::class, 'save']);
        Route::get('/detail', [SettingController::class, 'get']);
    });
    Route::get('/', [SettingController::class, 'get']);
});
