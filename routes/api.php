<?php

use Illuminate\Support\Facades\Route;
use VitalSaaS\VitalAccess\Http\Controllers\Api\VitalAccessApiController;

/*
|--------------------------------------------------------------------------
| VitalAccess API Routes
|--------------------------------------------------------------------------
|
| Here are the API routes for VitalAccess package functionality.
| These routes provide RESTful API endpoints for role and permission management.
|
*/

Route::middleware(['api', 'auth:sanctum'])->prefix('vitalaccess')->name('api.vitalaccess.')->group(function () {
    // Roles API
    Route::get('roles', [VitalAccessApiController::class, 'roles'])->name('roles.index');
    Route::get('roles/{role}', [VitalAccessApiController::class, 'showRole'])->name('roles.show');

    // Permissions API
    Route::get('permissions', [VitalAccessApiController::class, 'permissions'])->name('permissions.index');
    Route::get('permissions/{permission}', [VitalAccessApiController::class, 'showPermission'])->name('permissions.show');

    // Modules API
    Route::get('modules', [VitalAccessApiController::class, 'modules'])->name('modules.index');
    Route::get('modules/{module}', [VitalAccessApiController::class, 'showModule'])->name('modules.show');

    // User roles API
    Route::get('users/{user}/roles', [VitalAccessApiController::class, 'userRoles'])->name('users.roles.index');
    Route::post('users/{user}/roles', [VitalAccessApiController::class, 'assignRole'])->name('users.roles.assign');
    Route::delete('users/{user}/roles/{role}', [VitalAccessApiController::class, 'removeRole'])->name('users.roles.remove');

    // Permission checking API
    Route::post('check-permission', [VitalAccessApiController::class, 'checkPermission'])->name('check-permission');
    Route::get('user-permissions/{user}', [VitalAccessApiController::class, 'userPermissions'])->name('user-permissions');

    // Role hierarchy API
    Route::get('role-hierarchy', [VitalAccessApiController::class, 'roleHierarchy'])->name('role-hierarchy');
});