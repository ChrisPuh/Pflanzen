<?php

declare(strict_types=1);

namespace App\Filament\Resources\PlantCategories\Schemas;

use App\Enums\PlantCategory as PlantCategoryEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

final class PlantCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('name')
                    ->label('Plant Category')
                    ->options(collect(PlantCategoryEnum::cases())->mapWithKeys(
                        fn (PlantCategoryEnum $category) => [$category->value => $category->getLabel()]
                    ))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->searchable(),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->maxLength(500)
                    ->columnSpanFull(),
            ]);
    }
}
