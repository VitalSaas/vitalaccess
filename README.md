# VitalAccess - Advanced RBAC for Laravel

![VitalAccess Logo](https://via.placeholder.com/600x200/00a8cc/ffffff?text=VitalAccess+RBAC)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/vitalsaas/vitalaccess.svg?style=flat-square)](https://packagist.org/packages/vitalsaas/vitalaccess)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/vitalsaas/vitalaccess/run-tests?label=tests)](https://github.com/vitalsaas/vitalaccess/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/vitalsaas/vitalaccess/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/vitalsaas/vitalaccess/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/vitalsaas/vitalaccess.svg?style=flat-square)](https://packagist.org/packages/vitalsaas/vitalaccess)

**VitalAccess** is a powerful and truly **PLUG & PLAY** Role-Based Access Control (RBAC) system for Laravel applications with built-in Filament integration. Get a complete admin panel with roles, permissions, and dynamic navigation in just ONE COMMAND!

## 🚀 Features

- **🎯 PLUG & PLAY**: One command installation with automatic configuration
- **🔐 Complete RBAC System**: Roles, permissions, modules, and user management
- **🎨 Filament Integration**: Beautiful admin interface with dynamic navigation
- **👤 Auto User Setup**: Automatic User model enhancement with VitalAccess trait
- **🛡️ Middleware Integration**: Automatic permission middleware registration
- **📊 Admin Dashboard**: Full admin panel with user, role, and permission management
- **🔧 Dynamic Navigation**: Database-driven navigation with hierarchical structure  
- **⚡ High Performance**: Optimized queries with UUID primary keys
- **🎯 Hierarchical Permissions**: Support for complex permission structures
- **📱 Modern UI**: Clean and responsive Filament resources
- **🧪 Battle Tested**: Production-ready with comprehensive functionality

## 📋 Requirements

- PHP 8.2+
- Laravel 11.0+ or 12.0+
- Filament 5.0+ (optional, for admin interface)

## 🛠️ Installation

### 🎯 PLUG & PLAY Installation (Recommended)

Install the package and get a complete admin panel in just 2 commands:

```bash
composer require vitalsaas/vitalaccess
php artisan vitalaccess:install --filament --seed --force
```

**That's it! 🎉** You now have:
- ✅ **Complete admin panel** at `/admin`
- ✅ **Admin user**: `admin@vitalaccess.com` / `password` 
- ✅ **User model automatically enhanced** with RBAC methods
- ✅ **Middleware registered** for permission checking
- ✅ **Dynamic navigation** from database
- ✅ **Sample data** with roles, permissions, and modules

### 🌐 Access Your Admin Panel

**URL**: `your-domain.com/admin`  
**Email**: `admin@vitalaccess.com`  
**Password**: `password`  

⚠️ **Don't forget to change the default password!**

### 🔧 Custom Installation

For step-by-step installation:

```bash
# Install base system
php artisan vitalaccess:install

# Add Filament resources
php artisan vitalaccess:install --filament

# Add sample data and admin user
php artisan vitalaccess:install --seed
```

## 📖 Usage

### 🎯 User Permissions (HasVitalAccess Trait)

The installation automatically adds the `HasVitalAccess` trait to your User model:

```php
// ✅ Automatically available after installation!

// Check permissions
if ($user->hasPermission('users.create')) {
    // User can create users
}

// Check roles
if ($user->hasRole('admin')) {
    // User is an admin
}

// Check multiple permissions/roles
if ($user->hasAnyPermission(['users.create', 'users.edit'])) {
    // User has at least one permission
}

if ($user->hasAnyRole(['admin', 'manager'])) {
    // User has at least one role
}

// Assign/remove roles
$user->assignRole('manager');
$user->removeRole('manager');
$user->syncRoles(['admin', 'manager']);

// Check super admin
if ($user->isSuperAdmin()) {
    // User has superadmin role
}

// Check module access
if ($user->canAccessModule('users')) {
    // User can access users module
}

// Get all user permissions
$permissions = $user->getAllPermissions();
```

### 🛡️ Middleware Protection

Protect routes with the automatic middleware:

```php
// In routes/web.php
Route::middleware(['auth', 'vitalaccess:users.view'])->group(function () {
    Route::get('/admin/users', [UserController::class, 'index']);
});

// In controllers
public function __construct()
{
    $this->middleware('vitalaccess:users.create')->only(['create', 'store']);
    $this->middleware('vitalaccess:users.edit')->only(['edit', 'update']);
}
```

### 🔧 Advanced Usage

```php
use VitalSaaS\VitalAccess\Models\AccessRole;
use VitalSaaS\VitalAccess\Models\AccessPermission;

// Create roles and permissions
$role = AccessRole::create([
    'name' => 'Manager',
    'slug' => 'manager', 
    'description' => 'System manager',
    'is_active' => true,
]);

$permission = AccessPermission::create([
    'name' => 'Ver Usuarios',
    'slug' => 'users.view',
    'group' => 'users',
    'action' => 'view',
]);

// Assign permissions to roles
$role->permissions()->attach($permission->id);
```

### 🎨 Admin Panel Features

After installation, you get a complete admin panel at `/admin`:

- **📊 Dashboard**: Main admin dashboard with navigation
- **👥 Users Management**: Complete user CRUD with role assignments
- **🔐 Roles Management**: Create and manage roles with permissions  
- **🔑 Permissions Management**: Manage permissions by groups and actions
- **📦 Modules Management**: Configure dynamic navigation structure
- **🎯 Dynamic Navigation**: Navigation automatically built from database
- **🛡️ Permission-Based Access**: All resources protected by permissions
- **🎨 Modern UI**: Clean Filament interface with custom branding

### 🌐 Navigation Structure

VitalAccess creates a hierarchical navigation system:

```
VitalAccess/
├── Roles (Admin can manage roles)
├── Permissions (Admin can manage permissions)  
└── Modules (Admin can manage navigation)

Dashboard (Available to all users)
Administration/ (Admin group)
├── Users (User management)
├── Roles (Role management)
├── Permissions (Permission management)
└── Modules (Module management)
```

## ⚙️ Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag="vitalaccess-config"
```

Configuration options in `config/vitalaccess.php`:

```php
return [
    'filament' => [
        'enabled' => true,
        'navigation_group' => 'VitalAccess',
    ],
    'cache' => [
        'enabled' => true,
        'ttl' => 3600,
    ],
    'roles' => [
        'super_admin' => 'Super Administrador',
        'admin' => 'Administrador',
    ],
];
```

## 📊 Database Structure

VitalAccess creates the following tables:

- `access_roles` - System roles
- `access_permissions` - Available permissions  
- `access_modules` - System modules
- `access_role_permissions` - Role-permission relationships
- `access_permission_modules` - Permission-module relationships
- `access_user_roles` - User-role assignments
- `access_user_business_units` - Business unit assignments
- `access_role_categories` - Role categorization

## 🎨 Customization

### Custom Filament Resources

You can customize the published Filament resources:

```bash
php artisan vitalaccess:publish-filament --force
```

Then modify the resources in:
- `app/Filament/Resources/AccessRoleResource.php`
- `app/Filament/Resources/AccessPermissionResource.php`
- `app/Filament/Resources/AccessModuleResource.php`

### Custom Models

Extend the base models for additional functionality:

```php
use VitalSaaS\VitalAccess\Models\AccessRole as BaseAccessRole;

class AccessRole extends BaseAccessRole
{
    // Add your custom methods
    public function customMethod()
    {
        return $this->name . ' - Custom';
    }
}
```

## 📚 Available Commands

### 🛠️ Installation Commands

```bash
# Complete plug & play installation
php artisan vitalaccess:install --filament --seed --force

# Step by step installation
php artisan vitalaccess:install               # Base installation
php artisan vitalaccess:install --filament    # Add Filament resources
php artisan vitalaccess:install --seed        # Add sample data

# Publish specific components
php artisan vendor:publish --tag="vitalaccess-config"
php artisan vendor:publish --tag="vitalaccess-migrations"
php artisan vendor:publish --tag="vitalaccess-seeders"
php artisan vendor:publish --tag="vitalaccess-filament-resources"
```

### 🔧 Maintenance Commands

```bash
# Show system statistics
php artisan vitalaccess:maintenance --stats

# Sync permissions with modules  
php artisan vitalaccess:maintenance --sync

# Clean unused permissions
php artisan vitalaccess:maintenance --cleanup

# Run all maintenance tasks
php artisan vitalaccess:maintenance --stats --sync --cleanup
```

## 🧪 Testing

```bash
composer test
```

## 📈 Performance

VitalAccess is optimized for performance:

- **Efficient Queries**: Minimal database queries with proper eager loading
- **Caching Support**: Built-in caching for permissions and roles
- **Indexed Tables**: Proper database indexes for fast lookups
- **Lazy Loading**: Resources loaded only when needed

## 🔒 Security

- **SQL Injection Protection**: All queries use parameter binding
- **XSS Prevention**: Proper input sanitization
- **CSRF Protection**: Laravel CSRF protection enabled
- **Permission Validation**: Strict permission checking

## 🤝 Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## 🔗 Related Packages

- [Filament](https://github.com/filamentphp/filament) - Admin interface
- [Laravel Permission](https://github.com/spatie/laravel-permission) - Alternative RBAC
- [Laravel Sanctum](https://github.com/laravel/sanctum) - API authentication

## 📝 Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## 🔐 Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## 💖 Credits

- [VitalSaaS Team](https://github.com/VitalSaaS)
- [All Contributors](../../contributors)

---

<p align="center">
  <strong>Built with ❤️ by VitalSaaS</strong><br>
  <a href="https://vitalsaas.com">https://vitalsaas.com</a>
</p>