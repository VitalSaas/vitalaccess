<?php

namespace VitalSaaS\VitalAccess\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use VitalSaaS\VitalAccess\Models\AccessRole;

class AccessRoleResource extends Resource
{
    protected static string|null $model = AccessRole::class;

    protected static string|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|null $navigationLabel = 'Roles';

    protected static ?string $navigationGroup = 'VitalAccess';

    protected static int|null $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Rol')
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

                Forms\Components\Section::make('Configuración')
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

                Forms\Components\Section::make('Permisos')
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
