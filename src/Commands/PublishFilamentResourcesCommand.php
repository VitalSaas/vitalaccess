<?php

namespace VitalSaaS\VitalAccess\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PublishFilamentResourcesCommand extends Command
{
    protected $signature = 'vitalaccess:publish-filament
                           {--force : Force overwrite existing files}';

    protected $description = 'Publish VitalAccess Filament resources (Resources, Widgets, Pages)';

    public function handle(): int
    {
        $this->info('🎨 Publishing VitalAccess Filament resources...');

        // Check if Filament is installed
        if (!class_exists('Filament\FilamentServiceProvider')) {
            $this->error('❌ Filament not detected. Please install Filament first:');
            $this->line('   composer require filament/filament');
            return Command::FAILURE;
        }

        $force = $this->option('force');

        // Publish Resources
        $this->info('📁 Publishing Filament Resources...');
        Artisan::call('vendor:publish', [
            '--tag' => 'vitalaccess-filament-resources',
            '--force' => $force,
        ]);

        // Publish Widgets
        $this->info('📊 Publishing Filament Widgets...');
        Artisan::call('vendor:publish', [
            '--tag' => 'vitalaccess-filament-widgets',
            '--force' => $force,
        ]);

        // Publish Pages
        $this->info('📄 Publishing Filament Pages...');
        Artisan::call('vendor:publish', [
            '--tag' => 'vitalaccess-filament-pages',
            '--force' => $force,
        ]);

        $this->newLine();
        $this->info('✅ VitalAccess Filament resources published successfully!');
        $this->newLine();

        $this->line('🌐 <comment>Available Admin Panel URLs:</comment>');
        $this->line('   📊 Dashboard: <info>your-domain.com/admin</info>');
        $this->line('   👥 Users: <info>your-domain.com/admin/users</info>');
        $this->line('   🔐 Roles: <info>your-domain.com/admin/access-roles</info>');
        $this->line('   🔑 Permissions: <info>your-domain.com/admin/access-permissions</info>');
        $this->line('   📦 Modules: <info>your-domain.com/admin/access-modules</info>');

        $this->newLine();
        $this->line('💡 <comment>Tip:</comment> Clear your application cache to see the new resources:');
        $this->line('   php artisan config:clear');

        return Command::SUCCESS;
    }
}