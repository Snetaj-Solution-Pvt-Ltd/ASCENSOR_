<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TasksController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

$ac = AuthController::class;
$tsk = TasksController::class;

Route::group(['prefix' => 'auth'], function () use($ac) {
    Route::post('register', [$ac, 'createUser']);
    Route::post('login', [$ac, 'loginUser']);
    Route::group(['middleware' => 'auth:sanctum'], function() use($ac) {
        Route::post('logout', [$ac, 'logout']);
    });
});

Route::group(['middleware' => 'auth:sanctum'], function () use ($ac,$tsk) {
    Route::get('/users/list', [$ac, 'getUsers']);
    Route::post('/task/create', [$tsk, 'create']);
    Route::post('/task/update', [$tsk, 'updateTask']);
    Route::get('/task/list', [$tsk, 'listTasks']);
    Route::post('/task/delete', [$tsk, 'deleteTask']);
});

