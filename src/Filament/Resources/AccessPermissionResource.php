<?php

namespace VitalSaaS\VitalAccess\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use VitalSaaS\VitalAccess\Models\AccessPermission;

class AccessPermissionResource extends Resource
{
    protected static ?string $model = AccessPermission::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'Permisos';

    protected static string|null $navigationGroup = 'VitalAccess';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Permiso')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(100),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100),

                        Forms\Components\TextInput::make('group')
                            ->label('Grupo')
                            ->required()
                            ->maxLength(50),

                        Forms\Components\TextInput::make('action')
                            ->label('Acción')
                            ->required()
                            ->maxLength(50),
                    ])->columns(2),

                Forms\Components\Section::make('Descripción')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->maxLength(500),
                    ]),

                Forms\Components\Section::make('Configuración')
                    ->schema([
                        Forms\Components\Toggle::make('is_system')
                            ->label('Permiso del Sistema')
                            ->default(false),
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

                Tables\Columns\TextColumn::make('group')
                    ->label('Grupo')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('action')
                    ->label('Acción')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_system')
                    ->label('Sistema')
                    ->boolean(),

                Tables\Columns\TextColumn::make('roles_count')
                    ->label('Roles')
                    ->counts('roles'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->label('Grupo')
                    ->options(function () {
                        return AccessPermission::distinct()->pluck('group', 'group')->toArray();
                    }),

                Tables\Filters\TernaryFilter::make('is_system')
                    ->label('Permiso del Sistema'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('group');
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
            'index' => \VitalSaaS\VitalAccess\Filament\Resources\AccessPermissionResource\Pages\ListAccessPermissions::route('/'),
            'create' => \VitalSaaS\VitalAccess\Filament\Resources\AccessPermissionResource\Pages\CreateAccessPermission::route('/create'),
            'edit' => \VitalSaaS\VitalAccess\Filament\Resources\AccessPermissionResource\Pages\EditAccessPermission::route('/{record}/edit'),
        ];
    }
}
