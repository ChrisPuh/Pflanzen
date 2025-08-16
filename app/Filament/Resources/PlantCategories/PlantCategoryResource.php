<?php

declare(strict_types=1);

namespace App\Filament\Resources\PlantCategories;

use App\Filament\Resources\PlantCategories\Pages\CreatePlantCategory;
use App\Filament\Resources\PlantCategories\Pages\EditPlantCategory;
use App\Filament\Resources\PlantCategories\Pages\ListPlantCategories;
use App\Filament\Resources\PlantCategories\Schemas\PlantCategoryForm;
use App\Filament\Resources\PlantCategories\Tables\PlantCategoriesTable;
use App\Models\PlantCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

final class PlantCategoryResource extends Resource
{
    protected static ?string $model = PlantCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    public static function form(Schema $schema): Schema
    {
        return PlantCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PlantCategoriesTable::configure($table);
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
            'index' => ListPlantCategories::route('/'),
            'create' => CreatePlantCategory::route('/create'),
            'edit' => EditPlantCategory::route('/{record}/edit'),
        ];
    }
}
