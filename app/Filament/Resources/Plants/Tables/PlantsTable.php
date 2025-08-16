<?php

declare(strict_types=1);

namespace App\Filament\Resources\Plants\Tables;

use App\Models\PlantCategory;
use App\Models\PlantType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

final class PlantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Plant Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('latin_name')
                    ->label('Latin Name')
                    ->searchable()
                    ->sortable()
                    ->color('gray'),

                TextColumn::make('plantType.name')
                    ->label('Type')
                    ->formatStateUsing(fn ($record) => $record->plantType->name->label())
                    ->badge()
                    ->sortable(),

                TextColumn::make('plantCategories.name')
                    ->label('Categories')
                    ->formatStateUsing(fn ($record) => $record->plantCategories
                        ->map(fn ($category) => $category->name->label())
                        ->join(', ')
                    )
                    ->wrap()
                    ->limit(50),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(40)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (mb_strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    })
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('plant_type_id')
                    ->label('Plant Type')
                    ->relationship('plantType', 'name')
                    ->getOptionLabelFromRecordUsing(fn (PlantType $record): string => $record->name->label()),

                SelectFilter::make('plantCategories')
                    ->label('Categories')
                    ->relationship('plantCategories', 'name')
                    ->getOptionLabelFromRecordUsing(fn (PlantCategory $record): string => $record->name->label())
                    ->multiple(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }
}
