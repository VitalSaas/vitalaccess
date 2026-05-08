<?php

namespace VitalSaaS\VitalAccess\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ConfigurePanelCommand extends Command
{
    protected $signature = 'vitalaccess:configure-panel';

    protected $description = 'Automatically configure AdminPanelProvider with VitalAccess resources and widgets';

    public function handle()
    {
        $this->info('Configuring AdminPanelProvider with VitalAccess integration...');

        $panelPath = app_path('Providers/Filament/AdminPanelProvider.php');

        if (!File::exists($panelPath)) {
            $this->error('AdminPanelProvider.php not found. Make sure Filament is installed.');
            return Command::FAILURE;
        }

        $content = File::get($panelPath);

        // Check if already configured
        if (str_contains($content, 'AccessRoleResource')) {
            $this->info('ℹ️ AdminPanelProvider already configured with VitalAccess');
            return Command::SUCCESS;
        }

        // Add imports after existing use statements
        $imports = [
            'use VitalSaaS\VitalAccess\Filament\Resources\AccessRoleResource;',
            'use VitalSaaS\VitalAccess\Filament\Resources\AccessPermissionResource;',
            'use VitalSaaS\VitalAccess\Filament\Resources\AccessModuleResource;',
            'use VitalSaaS\VitalAccess\Filament\Widgets\VitalAccessStatsWidget;'
        ];

        // Find the last use statement
        $lastUsePosition = strrpos($content, 'use ');
        if ($lastUsePosition !== false) {
            $lineEndPosition = strpos($content, "\n", $lastUsePosition);
            $content = substr_replace($content, "\n" . implode("\n", $imports), $lineEndPosition, 0);
        }

        // Add resources to the panel configuration
        $resourcesConfig = '            ->resources([
                AccessRoleResource::class,
                AccessPermissionResource::class,
                AccessModuleResource::class,
            ])';

        // Find widgets section and add VitalAccessStatsWidget
        if (preg_match('/->widgets\(\[\s*([^]]+)\s*\]\)/', $content, $matches)) {
            $widgetsContent = trim($matches[1]);
            if (!empty($widgetsContent) && !str_ends_with($widgetsContent, ',')) {
                $widgetsContent .= ',';
            }
            $widgetsContent .= "\n                VitalAccessStatsWidget::class,";

            $newWidgetsConfig = "->widgets([\n                $widgetsContent\n            ])";
            $content = preg_replace('/->widgets\(\[\s*[^]]+\s*\]\)/', $newWidgetsConfig, $content);
        }

        // Add resources configuration before middleware
        if (str_contains($content, '->middleware([')) {
            $content = str_replace('->middleware([', $resourcesConfig . "\n            ->middleware([", $content);
        } else {
            // If no middleware found, add before authMiddleware
            $content = str_replace('->authMiddleware([', $resourcesConfig . "\n            ->authMiddleware([", $content);
        }

        File::put($panelPath, $content);

        $this->info('✅ AdminPanelProvider configured successfully!');
        $this->warn('🔄 Clear cache: php artisan cache:clear');
        $this->info('🌐 Access: /admin with VitalAccess resources');

        return Command::SUCCESS;
    }
}