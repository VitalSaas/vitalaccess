<?php

use Illuminate\Support\Facades\Route;
use VitalSaaS\VitalAccess\Http\Controllers\VitalAccessController;

/*
|--------------------------------------------------------------------------
| VitalAccess Web Routes
|--------------------------------------------------------------------------
|
| Here are the web routes for VitalAccess package functionality.
| These routes provide web interfaces for role and permission management.
|
*/

Route::middleware(['web', 'auth'])->prefix('vitalaccess')->name('vitalaccess.')->group(function () {
    // Dashboard routes
    Route::get('dashboard', [VitalAccessController::class, 'dashboard'])->name('dashboard');

    // Role management routes
    Route::get('roles', [VitalAccessController::class, 'roles'])->name('roles.index');
    Route::get('roles/{role}', [VitalAccessController::class, 'showRole'])->name('roles.show');

    // Permission management routes
    Route::get('permissions', [VitalAccessController::class, 'permissions'])->name('permissions.index');
    Route::get('permissions/{permission}', [VitalAccessController::class, 'showPermission'])->name('permissions.show');

    // Module management routes
    Route::get('modules', [VitalAccessController::class, 'modules'])->name('modules.index');
    Route::get('modules/{module}', [VitalAccessController::class, 'showModule'])->name('modules.show');

    // User role assignment routes
    Route::get('users/{user}/roles', [VitalAccessController::class, 'userRoles'])->name('users.roles');
    Route::post('users/{user}/roles', [VitalAccessController::class, 'assignRole'])->name('users.roles.assign');
    Route::delete('users/{user}/roles/{role}', [VitalAccessController::class, 'removeRole'])->name('users.roles.remove');
});