<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::fallback(function () {
    return view('errors.404');
});

Route::prefix('roles')
    ->controller(RoleController::class)
    ->group(function () {
        Route::post('/', 'createRole');
        Route::post('/batch', 'createBatchRole');
        Route::post('/search', 'searchRoles');
        Route::post('/destroy', 'deleteBatchRole');
        Route::delete('/destroy/{id}', 'deleteRole');
        Route::put('/{id}', 'updateRole');
        Route::get('/{id}', 'getRole');
    });

Route::prefix('users')
    ->controller(UserController::class)
    ->group(function () {
        Route::post('/', 'createUser');
        Route::post('/search', 'searchUser');
        Route::put('/{id}', 'updateUser');
        Route::get('/{id}', 'getUser');
        Route::delete('/{id}', 'deleteUser');
    });

Route::prefix('transactions')
    ->controller(TransactionController::class)
    ->group(function () {
        Route::post('/', 'createBatchTransaction');
    });
