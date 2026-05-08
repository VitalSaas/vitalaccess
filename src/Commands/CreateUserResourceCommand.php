<?php

namespace VitalSaaS\VitalAccess\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateUserResourceCommand extends Command
{
    protected $signature = 'vitalaccess:user-resource';

    protected $description = 'Create UserResource with VitalAccess integration and correct Filament 5.x imports';

    public function handle()
    {
        $this->info('Creating UserResource with VitalAccess integration...');

        // Create directories
        $resourcePath = app_path('Filament/Resources/Users');
        $schemaPath = $resourcePath . '/Schemas';
        $tablePath = $resourcePath . '/Tables';
        $pagesPath = $resourcePath . '/Pages';

        File::ensureDirectoryExists($resourcePath);
        File::ensureDirectoryExists($schemaPath);
        File::ensureDirectoryExists($tablePath);
        File::ensureDirectoryExists($pagesPath);

        // Create UserResource
        File::put($resourcePath . '/UserResource.php', $this->getUserResourceStub());

        // Create UserForm
        File::put($schemaPath . '/UserForm.php', $this->getUserFormStub());

        // Create UsersTable
        File::put($tablePath . '/UsersTable.php', $this->getUsersTableStub());

        // Create Pages
        File::put($pagesPath . '/ListUsers.php', $this->getListUsersStub());
        File::put($pagesPath . '/CreateUser.php', $this->getCreateUserStub());
        File::put($pagesPath . '/EditUser.php', $this->getEditUserStub());

        // Update User model
        $this->updateUserModel();

        $this->info('✅ UserResource created successfully with VitalAccess integration!');
        $this->warn('🔄 Clear cache: php artisan cache:clear');
        $this->info('🌐 Access: /admin/users');

        return Command::SUCCESS;
    }

    private function getUserResourceStub(): string
    {
        return '<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static BackedEnum|string|null $navigationIcon = \'heroicon-o-users\';

    protected static string|null $navigationLabel = \'Usuarios\';

    protected static UnitEnum|string|null $navigationGroup = \'VitalAccess\';

    protected static int|null $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            \'index\' => ListUsers::route(\'/\'),
            \'create\' => CreateUser::route(\'/create\'),
            \'edit\' => EditUser::route(\'/{record}/edit\'),
        ];
    }
}';
    }

    private function getUserFormStub(): string
    {
        return '<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(\'Información Personal\')
                    ->schema([
                        TextInput::make(\'name\')
                            ->label(\'Nombre\')
                            ->required()
                            ->maxLength(255),

                        TextInput::make(\'email\')
                            ->label(\'Correo Electrónico\')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make(\'password\')
                            ->label(\'Contraseña\')
                            ->password()
                            ->required(fn (string $context): bool => $context === \'create\')
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->confirmed(),

                        TextInput::make(\'password_confirmation\')
                            ->label(\'Confirmar Contraseña\')
                            ->password()
                            ->required(fn (string $context): bool => $context === \'create\')
                            ->dehydrated(false),
                    ])->columns(2),

                Section::make(\'Información de Verificación\')
                    ->schema([
                        DateTimePicker::make(\'email_verified_at\')
                            ->label(\'Email Verificado\')
                            ->nullable(),
                    ]),

                Section::make(\'Roles y Permisos\')
                    ->schema([
                        CheckboxList::make(\'accessRoles\')
                            ->label(\'Roles\')
                            ->relationship(\'accessRoles\', \'name\')
                            ->columns(2)
                            ->searchable(),
                    ]),
            ]);
    }
}';
    }

    private function getUsersTableStub(): string
    {
        return '<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(\'name\')
                    ->label(\'Nombre\')
                    ->searchable()
                    ->sortable(),

                TextColumn::make(\'email\')
                    ->label(\'Correo Electrónico\')
                    ->searchable()
                    ->sortable(),

                BadgeColumn::make(\'accessRoles.name\')
                    ->label(\'Roles\')
                    ->searchable()
                    ->separator(\', \')
                    ->color(\'success\'),

                IconColumn::make(\'email_verified_at\')
                    ->label(\'Verificado\')
                    ->boolean()
                    ->sortable(),

                TextColumn::make(\'created_at\')
                    ->label(\'Creado\')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make(\'updated_at\')
                    ->label(\'Actualizado\')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make(\'email_verified_at\')
                    ->label(\'Email Verificado\')
                    ->nullable(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort(\'created_at\', \'desc\');
    }
}';
    }

    private function getListUsersStub(): string
    {
        return '<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}';
    }

    private function getCreateUserStub(): string
    {
        return '<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}';
    }

    private function getEditUserStub(): string
    {
        return '<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}';
    }

    private function updateUserModel(): void
    {
        $modelPath = app_path('Models/User.php');

        if (File::exists($modelPath)) {
            $content = File::get($modelPath);

            // Check if trait is already added
            if (!str_contains($content, 'HasVitalAccess')) {
                // Add import
                $content = str_replace(
                    'use Illuminate\Notifications\Notifiable;',
                    'use Illuminate\Notifications\Notifiable;' . PHP_EOL . 'use VitalSaaS\VitalAccess\Traits\HasVitalAccess;',
                    $content
                );

                // Add trait usage
                $content = str_replace(
                    'use HasFactory, Notifiable;',
                    'use HasFactory, Notifiable, HasVitalAccess;',
                    $content
                );

                File::put($modelPath, $content);
                $this->info('✅ User model updated with HasVitalAccess trait');
            } else {
                $this->info('ℹ️ User model already has VitalAccess integration');
            }
        }
    }
}