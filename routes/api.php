<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttenderController;

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
    });
    Route::group([
        'prefix' => 'attender',
        'middleware' => 'check_token',
    ], function() {
        Route::get('/{id}', [AttenderController::class, 'detail'])->name('attender.detail');
    });
    Route::group([
        'prefix' => 'attender',
        'middleware' => ['check_token', 'only_admin'],
    ], function() {
        Route::get('/active/{id}', [AttenderController::class, 'activeStatus'])->name('attender.activeStatus');
        Route::get('/inactive/{id}', [AttenderController::class, 'inactiveStatus'])->name('attender.inactiveStatus');
        Route::put('/{id}', [AttenderController::class, 'edit'])->name('attender.edit');
        Route::delete('/{id}', [AttenderController::class, 'delete'])->name('attender.delete');
    });
});
