<?php

declare(strict_types=1);

namespace App\Filament\Resources\PlantTypes;

use App\Filament\Resources\PlantTypes\Pages\CreatePlantType;
use App\Filament\Resources\PlantTypes\Pages\EditPlantType;
use App\Filament\Resources\PlantTypes\Pages\ListPlantTypes;
use App\Filament\Resources\PlantTypes\Schemas\PlantTypeForm;
use App\Filament\Resources\PlantTypes\Tables\PlantTypesTable;
use App\Models\PlantType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

final class PlantTypeResource extends Resource
{
    protected static ?string $model = PlantType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    public static function form(Schema $schema): Schema
    {
        return PlantTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PlantTypesTable::configure($table);
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
            'index' => ListPlantTypes::route('/'),
            'create' => CreatePlantType::route('/create'),
            'edit' => EditPlantType::route('/{record}/edit'),
        ];
    }
}
