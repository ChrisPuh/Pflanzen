<?php

declare(strict_types=1);

namespace App\Filament\Resources\PlantTypes\Schemas;

use App\Enums\PlantType as PlantTypeEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

final class PlantTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('name')
                    ->label('Plant Type')
                    ->options(collect(PlantTypeEnum::cases())->mapWithKeys(
                        fn (PlantTypeEnum $type) => [$type->value => $type->label()]
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
