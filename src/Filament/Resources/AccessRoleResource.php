<?php

namespace VitalSaaS\VitalAccess\Filament\Resources;

use BackedEnum;
use UnitEnum;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use VitalSaaS\VitalAccess\Models\AccessRole;

class AccessRoleResource extends Resource
{
    protected static ?string $model = AccessRole::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|null $navigationLabel = 'Roles';

    protected static UnitEnum|string|null $navigationGroup = 'VitalAccess';

    protected static int|null $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Información del Rol')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(100),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->maxLength(500),

                        Forms\Components\Select::make('category_id')
                            ->label('Categoría')
                            ->relationship('category', 'name')
                            ->nullable(),
                    ])->columns(2),

                Section::make('Configuración')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true),

                        Forms\Components\Toggle::make('is_system')
                            ->label('Rol del Sistema')
                            ->default(false),

                        Forms\Components\TextInput::make('level')
                            ->label('Nivel')
                            ->numeric()
                            ->default(1),
                    ])->columns(3),

                Section::make('Permisos')
                    ->schema([
                        Forms\Components\CheckboxList::make('permissions')
                            ->relationship('permissions', 'name')
                            ->columns(2)
                            ->searchable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoría')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_system')
                    ->label('Sistema')
                    ->boolean(),

                Tables\Columns\TextColumn::make('level')
                    ->label('Nivel')
                    ->sortable(),

                Tables\Columns\TextColumn::make('users_count')
                    ->label('Usuarios')
                    ->counts('users'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado'),

                Tables\Filters\TernaryFilter::make('is_system')
                    ->label('Rol del Sistema'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => \VitalSaaS\VitalAccess\Filament\Resources\AccessRoleResource\Pages\ListAccessRoles::route('/'),
            'create' => \VitalSaaS\VitalAccess\Filament\Resources\AccessRoleResource\Pages\CreateAccessRole::route('/create'),
            'edit' => \VitalSaaS\VitalAccess\Filament\Resources\AccessRoleResource\Pages\EditAccessRole::route('/{record}/edit'),
        ];
    }
}