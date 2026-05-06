<?php

namespace VitalSaaS\VitalAccess\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase;
use VitalSaaS\VitalAccess\Models\AccessRole;
use VitalSaaS\VitalAccess\Models\AccessPermission;
use VitalSaaS\VitalAccess\VitalAccessServiceProvider;

class VitalAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function getPackageProviders($app)
    {
        return [VitalAccessServiceProvider::class];
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    /** @test */
    public function it_can_create_a_role()
    {
        $role = AccessRole::create([
            'name' => 'Test Role',
            'slug' => 'test-role',
            'description' => 'A test role',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('access_roles', [
            'name' => 'Test Role',
            'slug' => 'test-role',
        ]);
    }

    /** @test */
    public function it_can_create_a_permission()
    {
        $permission = AccessPermission::create([
            'name' => 'Test Permission',
            'slug' => 'test.permission',
            'group' => 'test',
            'action' => 'permission',
            'description' => 'A test permission',
        ]);

        $this->assertDatabaseHas('access_permissions', [
            'name' => 'Test Permission',
            'slug' => 'test.permission',
        ]);
    }

    /** @test */
    public function it_can_assign_permission_to_role()
    {
        $role = AccessRole::create([
            'name' => 'Test Role',
            'slug' => 'test-role',
        ]);

        $permission = AccessPermission::create([
            'name' => 'Test Permission',
            'slug' => 'test.permission',
            'group' => 'test',
            'action' => 'permission',
        ]);

        $role->permissions()->attach($permission->id);

        $this->assertTrue($role->permissions()->where('id', $permission->id)->exists());
    }

    /** @test */
    public function middleware_is_registered()
    {
        $router = $this->app['router'];
        $middleware = $router->getMiddleware();

        $this->assertArrayHasKey('vitalaccess', $middleware);
    }
}
