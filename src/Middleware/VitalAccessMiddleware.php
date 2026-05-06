<?php

namespace VitalSaaS\VitalAccess\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use VitalSaaS\VitalAccess\Models\AccessRole;
use VitalSaaS\VitalAccess\Models\AccessPermission;

class VitalAccessMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permission = null)
    {
        if (!Auth::check()) {
            return redirect()->route('filament.admin.auth.login');
        }

        $user = Auth::user();

        // If no specific permission is required, just check if user has any role
        if (!$permission) {
            if ($this->userHasAnyRole($user)) {
                return $next($request);
            }
            abort(403, 'Access denied. No roles assigned.');
        }

        // Check if user has the specific permission
        if ($this->userHasPermission($user, $permission)) {
            return $next($request);
        }

        abort(403, 'Access denied. Required permission: ' . $permission);
    }

    /**
     * Check if user has any role assigned
     */
    protected function userHasAnyRole($user): bool
    {
        return $user->accessRoles()->exists();
    }

    /**
     * Check if user has specific permission
     */
    protected function userHasPermission($user, string $permission): bool
    {
        // Get user roles
        $userRoles = $user->accessRoles()->get();

        if ($userRoles->isEmpty()) {
            return false;
        }

        // Check if any role has the required permission
        foreach ($userRoles as $role) {
            if ($role->permissions()->where('slug', $permission)->exists()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get user permissions for current request context
     */
    public static function getUserPermissions($user): array
    {
        if (!$user) {
            return [];
        }

        $permissions = [];
        $userRoles = $user->accessRoles()->with('permissions')->get();

        foreach ($userRoles as $role) {
            foreach ($role->permissions as $permission) {
                $permissions[] = $permission->slug;
            }
        }

        return array_unique($permissions);
    }
}