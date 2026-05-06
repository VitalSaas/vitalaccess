<?php

namespace VitalSaaS\VitalAccess\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InstallVitalAccessCommand extends Command
{
    protected $signature = 'vitalaccess:install {--filament : Install Filament resources and configure panel} {--seed : Run seeders and create admin user} {--force : Force overwrite existing files}';

    protected $description = 'Install VitalAccess RBAC system - Plug and Play setup';

    public function handle(): int
    {
        $this->info('🚀 Installing VitalAccess RBAC System...');

        // Publish configuration
        $this->publishConfig();

        // Publish migrations
        $this->publishMigrations();

        // Publish models
        $this->publishModels();

        // Configure User model
        $this->configureUserModel();

        // Register middleware
        $this->registerMiddleware();

        // Run migrations
        $this->runMigrations();

        // Install Filament resources if requested
        if ($this->option('filament')) {
            $this->installFilamentResources();
            $this->configurePanelProvider();
        }

        // Publish and run seeders
        $this->publishSeeders();

        if ($this->option('seed')) {
            $this->runSeeders();
            $this->createAdminUser();
        }

        $this->displayCompletionMessage();

        return Command::SUCCESS;
    }

    protected function publishConfig(): void
    {
        $this->info('📝 Publishing configuration...');
        Artisan::call('vendor:publish', [
            '--tag' => 'vitalaccess-config',
            '--force' => $this->option('force'),
        ]);
    }

    protected function publishMigrations(): void
    {
        $this->info('🗃️ Publishing migrations...');
        Artisan::call('vendor:publish', [
            '--tag' => 'vitalaccess-migrations',
            '--force' => $this->option('force'),
        ]);
    }

    protected function publishModels(): void
    {
        $this->info('🏗️ Publishing models...');
        Artisan::call('vendor:publish', [
            '--tag' => 'vitalaccess-models',
            '--force' => $this->option('force'),
        ]);
    }

    protected function runMigrations(): void
    {
        $this->info('🔄 Running migrations...');
        Artisan::call('migrate', ['--force' => true]);
        $this->line('   Migrations completed successfully');
    }

    protected function installFilamentResources(): void
    {
        $this->info('🎨 Configuring Filament integration...');

        // Check if Filament is installed
        if (!class_exists('Filament\FilamentServiceProvider')) {
            $this->warn('⚠️  Filament not detected. Please install Filament first:');
            $this->line('   composer require filament/filament');
            return;
        }

        // Note: Resources are auto-registered by the service provider for plug-and-play functionality
        $this->line('   ✅ VitalAccess resources auto-registered with Filament');
        $this->line('   🎛️ Admin resources for Roles, Permissions, and Modules available');
        $this->line('   📊 Navigation group "VitalAccess" added to admin panel');

        // Only publish if user wants to customize (optional)
        if ($this->option('force') || $this->confirm('Do you want to publish Filament resources for customization? (optional)', false)) {
            Artisan::call('vendor:publish', [
                '--tag' => 'vitalaccess-filament-resources-custom',
                '--force' => $this->option('force'),
            ]);

            $this->line('   📝 Custom Filament resources published for editing');
            $this->warn('   ⚠️  Remember to update namespaces if customizing published files');
        }
    }

    protected function publishSeeders(): void
    {
        $this->info('🌱 Publishing seeders...');
        Artisan::call('vendor:publish', [
            '--tag' => 'vitalaccess-seeders',
            '--force' => $this->option('force'),
        ]);
    }

    protected function runSeeders(): void
    {
        $this->info('🌱 Running seeders...');

        if (File::exists(database_path('seeders/VitalAccessSeeder.php'))) {
            Artisan::call('db:seed', ['--class' => 'VitalAccessSeeder']);
            $this->line('   ✅ Sample data created successfully');
        } else {
            $this->warn('   ⚠️  VitalAccessSeeder not found, skipping...');
        }
    }

    protected function displayCompletionMessage(): void
    {
        $this->newLine();
        $this->info('🎉 VitalAccess PLUG & PLAY installation completed successfully!');
        $this->newLine();

        $this->line('✅ <comment>What was configured:</comment>');
        $this->line('   📦 Models & migrations published and executed');
        $this->line('   👤 User model enhanced with HasVitalAccess trait');
        $this->line('   🛡️ VitalAccess middleware registered');

        if ($this->option('seed')) {
            $this->line('   🌱 Database seeded with roles, permissions & modules');
            $this->line('   👨‍💼 Admin user created (admin@vitalaccess.com / password)');
        }

        if ($this->option('filament')) {
            $this->line('   🎨 Filament resources configured');
            $this->line('   🎛️ Dynamic navigation from database modules');
            $this->line('   🔐 Permission-based access control');
        }

        $this->newLine();

        if ($this->option('filament') && $this->option('seed')) {
            $this->line('🚀 <comment>Ready to use! Access your admin panel:</comment>');
            $this->line('   🌐 URL: <info>your-domain.com/admin</info>');
            $this->line('   📧 Email: <info>admin@vitalaccess.com</info>');
            $this->line('   🔑 Password: <info>password</info>');
            $this->warn('   ⚠️  Please change the default password!');
            $this->newLine();
        } else {
            $this->line('🛠️ <comment>Complete your setup:</comment>');
            if (!$this->option('seed')) {
                $this->line('   1. Run seeders: <info>php artisan vitalaccess:install --seed --filament</info>');
            }
            if (!$this->option('filament')) {
                $this->line('   2. Enable Filament: <info>php artisan vitalaccess:install --filament --seed</info>');
            }
        }

        $this->line('📚 <comment>Documentation:</comment> https://github.com/VitalSaaS/vitalaccess');
        $this->line('💡 <comment>Usage:</comment> Use $user->hasRole(), $user->hasPermission(), etc.');

        $this->newLine();
        $this->info('🎯 VitalAccess is now PLUG & PLAY ready!');
    }

    protected function configureUserModel(): void
    {
        $this->info('👤 Configuring User model...');

        $userModelPath = app_path('Models/User.php');

        if (File::exists($userModelPath)) {
            $content = File::get($userModelPath);

            // Check if trait is already added
            if (!str_contains($content, 'use VitalSaaS\VitalAccess\Traits\HasVitalAccess;')) {
                // Add the use statement
                $content = str_replace(
                    'use Illuminate\Foundation\Auth\User as Authenticatable;',
                    "use Illuminate\Foundation\Auth\User as Authenticatable;\nuse VitalSaaS\VitalAccess\Traits\HasVitalAccess;",
                    $content
                );

                // Add the trait to the class
                $content = str_replace(
                    'use HasApiTokens, HasFactory, Notifiable;',
                    'use HasApiTokens, HasFactory, Notifiable, HasVitalAccess;',
                    $content
                );

                File::put($userModelPath, $content);
                $this->line('   ✅ HasVitalAccess trait added to User model');
            } else {
                $this->line('   ⏭️  HasVitalAccess trait already configured');
            }
        } else {
            $this->warn('   ⚠️  User model not found at expected location');
        }
    }

    protected function registerMiddleware(): void
    {
        $this->info('🛡️ Registering middleware...');

        $kernelPath = app_path('Http/Kernel.php');

        if (File::exists($kernelPath)) {
            $content = File::get($kernelPath);

            // Check if middleware is already registered
            if (!str_contains($content, 'vitalaccess')) {
                // Add middleware alias
                $middlewareAlias = "        'vitalaccess' => \\VitalSaaS\\VitalAccess\\Middleware\\VitalAccessMiddleware::class,";

                $content = str_replace(
                    "protected \$middlewareAliases = [",
                    "protected \$middlewareAliases = [\n$middlewareAlias",
                    $content
                );

                File::put($kernelPath, $content);
                $this->line('   ✅ VitalAccess middleware registered');
            } else {
                $this->line('   ⏭️  VitalAccess middleware already registered');
            }
        } else {
            $this->warn('   ⚠️  Kernel.php not found, middleware not registered');
        }
    }

    protected function configurePanelProvider(): void
    {
        $this->info('🎛️ Configuring Filament panel...');

        $panelProviderPath = app_path('Providers/Filament');

        if (!File::exists($panelProviderPath)) {
            File::makeDirectory($panelProviderPath, 0755, true);
        }

        $panelProviderFile = $panelProviderPath . '/AdminPanelProvider.php';

        // Create a custom panel provider that extends VitalAccess
        $panelProviderContent = <<<'PHP'
<?php

namespace App\Providers\Filament;

use Filament\Panel;
use VitalSaaS\VitalAccess\VitalAccessPanelProvider;

class AdminPanelProvider extends VitalAccessPanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return parent::panel($panel)
            ->brandName('VitalAccess Admin')
            ->favicon(asset('favicon.ico'));
    }
}
PHP;

        if (!File::exists($panelProviderFile) || $this->option('force')) {
            File::put($panelProviderFile, $panelProviderContent);
            $this->line('   ✅ Custom panel provider created');
        }

        // Register in app config
        $this->registerPanelProvider();
    }

    protected function registerPanelProvider(): void
    {
        $configPath = config_path('app.php');

        if (File::exists($configPath)) {
            $content = File::get($configPath);

            if (!str_contains($content, 'App\\Providers\\Filament\\AdminPanelProvider')) {
                $content = str_replace(
                    "App\\Providers\\RouteServiceProvider::class,",
                    "App\\Providers\\RouteServiceProvider::class,\n        App\\Providers\\Filament\\AdminPanelProvider::class,",
                    $content
                );

                File::put($configPath, $content);
                $this->line('   ✅ Panel provider registered in config');
            }
        }
    }

    protected function createAdminUser(): void
    {
        $this->info('👨‍💼 Creating admin user...');

        try {
            $userModel = config('auth.providers.users.model', 'App\\Models\\User');

            // Check if admin user already exists
            $existingUser = $userModel::where('email', 'admin@vitalaccess.com')->first();

            if (!$existingUser) {
                $user = $userModel::create([
                    'name' => 'VitalAccess Admin',
                    'email' => 'admin@vitalaccess.com',
                    'email_verified_at' => now(),
                    'password' => bcrypt('password'),
                ]);

                // Assign superadmin role
                $user->assignRole('superadmin');

                $this->line('   ✅ Admin user created successfully');
                $this->line('   📧 Email: admin@vitalaccess.com');
                $this->line('   🔑 Password: password');
                $this->warn('   ⚠️  Please change the default password after first login!');
            } else {
                // Ensure existing user has superadmin role
                if (!$existingUser->hasRole('superadmin')) {
                    $existingUser->assignRole('superadmin');
                    $this->line('   ✅ Superadmin role assigned to existing user');
                } else {
                    $this->line('   ⏭️  Admin user already exists and configured');
                }
            }

        } catch (\Exception $e) {
            $this->error('   ❌ Failed to create admin user: ' . $e->getMessage());
        }
    }
}