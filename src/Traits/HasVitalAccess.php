<?php

namespace VitalSaaS\VitalAccess\Traits;

use VitalSaaS\VitalAccess\Models\AccessRole;
use VitalSaaS\VitalAccess\Models\AccessPermission;

trait HasVitalAccess
{
    /**
     * Get the roles for the user.
     */
    public function accessRoles()
    {
        return $this->belongsToMany(
            AccessRole::class,
            'access_user_roles',
            'user_id',
            'role_id'
        )->withTimestamps();
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->accessRoles()->where('slug', $roleSlug)->exists();
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roleSlugs): bool
    {
        return $this->accessRoles()->whereIn('slug', $roleSlugs)->exists();
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->accessRoles()
            ->whereHas('permissions', function ($query) use ($permissionSlug) {
                $query->where('slug', $permissionSlug);
            })
            ->exists();
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(array $permissionSlugs): bool
    {
        return $this->accessRoles()
            ->whereHas('permissions', function ($query) use ($permissionSlugs) {
                $query->whereIn('slug', $permissionSlugs);
            })
            ->exists();
    }

    /**
     * Get all permissions for the user through their roles
     */
    public function getAllPermissions()
    {
        return AccessPermission::whereHas('roles.users', function ($query) {
            $query->where('users.id', $this->id);
        })->get();
    }

    /**
     * Assign a role to the user
     */
    public function assignRole(string $roleSlug): void
    {
        $role = AccessRole::where('slug', $roleSlug)->firstOrFail();

        if (!$this->accessRoles()->where('role_id', $role->id)->exists()) {
            $this->accessRoles()->attach($role->id, [
                'id' => \Str::uuid()->toString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Remove a role from the user
     */
    public function removeRole(string $roleSlug): void
    {
        $role = AccessRole::where('slug', $roleSlug)->first();

        if ($role) {
            $this->accessRoles()->detach($role->id);
        }
    }

    /**
     * Sync user roles
     */
    public function syncRoles(array $roleSlugs): void
    {
        $roles = AccessRole::whereIn('slug', $roleSlugs)->get();
        $roleIds = [];

        foreach ($roles as $role) {
            $roleIds[$role->id] = [
                'id' => \Str::uuid()->toString(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $this->accessRoles()->sync($roleIds);
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('superadmin');
    }

    /**
     * Check if user can access a specific module
     */
    public function canAccessModule(string $moduleSlug): bool
    {
        // Super admins can access everything
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Check if user has permissions for this module
        $modulePermissions = AccessPermission::whereHas('modules', function ($query) use ($moduleSlug) {
            $query->where('slug', $moduleSlug);
        })->pluck('slug')->toArray();

        if (empty($modulePermissions)) {
            return false;
        }

        return $this->hasAnyPermission($modulePermissions);
    }
}