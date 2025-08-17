<?php

declare(strict_types=1);

namespace App\Filament\Resources\Plants\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class PlantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Plant Information')
                    ->description('Basic information about the plant')
                    ->schema([
                        TextInput::make('name')
                            ->label('Plant Name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true),

                        TextInput::make('latin_name')
                            ->label('Latin Name')
                            ->maxLength(255)
                            ->placeholder('e.g., Rosa rubiginosa'),

                        Select::make('plant_type_id')
                            ->label('Plant Type')
                            ->relationship(
                                name: 'plantType',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (\Illuminate\Database\Eloquent\Builder $query) => $query->orderBy('name')
                            )
                            ->getOptionLabelFromRecordUsing(fn (\App\Models\PlantType $record): string => $record->name->getLabel())
                            ->required()
                            ->searchable()
                            ->preload(),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Categories')
                    ->description('Select categories that apply to this plant')
                    ->schema([
                        CheckboxList::make('categories')
                            ->label('Plant Categories')
                            ->relationship(
                                name: 'categories',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (\Illuminate\Database\Eloquent\Builder $query) => $query->orderBy('name')
                            )
                            ->getOptionLabelFromRecordUsing(fn (\App\Models\Category $record): string => $record->name->getLabel())
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(3),
                    ]),
            ]);
    }
}
