<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::fallback(function () {
    return view('errors.404');
});

Route::prefix('roles')->controller(RoleController::class)->group(function () {
    // Create
    Route::post('/', 'createRole');
    Route::post('/batch', 'createBatchRole');

    // View
    Route::post('/search', 'searchRoles');

    // Delete
    Route::post('/destroy', 'deleteBatchRole');
    Route::delete('/destroy/{id}', 'deleteRole');

    // Edit
    Route::put('/{id}', 'updateRole');

    Route::get('/{id}', 'getRole')->whereNumber('id');
});
