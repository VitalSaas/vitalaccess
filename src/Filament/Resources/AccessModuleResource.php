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
use VitalSaaS\VitalAccess\Models\AccessModule;

class AccessModuleResource extends Resource
{
    protected static ?string $model = AccessModule::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string|null $navigationLabel = 'Módulos';

    protected static UnitEnum|string|null $navigationGroup = 'VitalAccess';

    protected static int|null $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Información del Módulo')
                    ->schema([
                        Forms\Components\Select::make('parent_id')
                            ->label('Módulo Padre')
                            ->relationship('parent', 'name')
                            ->nullable()
                            ->searchable(),

                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(100),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100),

                        Forms\Components\TextInput::make('icon')
                            ->label('Icono')
                            ->maxLength(100)
                            ->placeholder('heroicon-o-home'),
                    ])->columns(2),

                Section::make('Configuración de Navegación')
                    ->schema([
                        Forms\Components\TextInput::make('route')
                            ->label('Ruta')
                            ->maxLength(200),

                        Forms\Components\Select::make('type')
                            ->label('Tipo')
                            ->options([
                                'menu' => 'Menú',
                                'group' => 'Grupo',
                                'divider' => 'Separador',
                            ])
                            ->default('menu')
                            ->required(),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('Orden')
                            ->numeric()
                            ->default(0),

                        Forms\Components\TextInput::make('depth')
                            ->label('Profundidad')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),

                Section::make('Estado y Visibilidad')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true),

                        Forms\Components\Toggle::make('is_visible')
                            ->label('Visible')
                            ->default(true),

                        Forms\Components\TextInput::make('plan_required')
                            ->label('Plan Requerido')
                            ->maxLength(50),

                        Forms\Components\Select::make('badge_type')
                            ->label('Tipo de Badge')
                            ->options([
                                'new' => 'Nuevo',
                                'beta' => 'Beta',
                                'pro' => 'Pro',
                                'premium' => 'Premium',
                            ])
                            ->nullable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($state, $record) {
                        $prefix = str_repeat('— ', $record->depth ?? 0);
                        return $prefix . $state;
                    }),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Padre')
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'menu' => 'success',
                        'group' => 'info',
                        'divider' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('route')
                    ->label('Ruta')
                    ->limit(30),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Orden')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_visible')
                    ->label('Visible')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'menu' => 'Menú',
                        'group' => 'Grupo',
                        'divider' => 'Separador',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Activo'),

                Tables\Filters\TernaryFilter::make('is_visible')
                    ->label('Visible'),
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
            ->defaultSort('sort_order')
            ->reorderable('sort_order');
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
            'index' => \VitalSaaS\VitalAccess\Filament\Resources\AccessModuleResource\Pages\ListAccessModules::route('/'),
            'create' => \VitalSaaS\VitalAccess\Filament\Resources\AccessModuleResource\Pages\CreateAccessModule::route('/create'),
            'edit' => \VitalSaaS\VitalAccess\Filament\Resources\AccessModuleResource\Pages\EditAccessModule::route('/{record}/edit'),
        ];
    }
}