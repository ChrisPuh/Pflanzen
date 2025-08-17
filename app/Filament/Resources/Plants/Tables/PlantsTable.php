<?php

declare(strict_types=1);

namespace App\Filament\Resources\Plants\Tables;

use App\Enums\PlantTypeEnum;
use App\Models\Category;
use App\Models\PlantType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                    ->formatStateUsing(fn (PlantTypeEnum $state): string => $state->getLabel())
                    ->badge()
                    ->sortable(),

                TextColumn::make('categories.name')
                    ->label('Categories')
                    ->formatStateUsing(fn (\App\Enums\PlantCategoryEnum $state): string => $state->getLabel())
                    ->badge()
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
                    ->options(
                        PlantType::all()->mapWithKeys(fn (PlantType $plantType) => [$plantType->id => $plantType->name->getLabel()])
                    ),
                Filter::make('categories')
                    ->label('Categories')
                    ->schema([
                        CheckboxList::make('category_ids')
                            ->label('Select Categories')
                            ->options(
                                Category::all()->mapWithKeys(fn (Category $category) => [$category->id => $category->name->getLabel()])
                            )
                            ->columns(2)
                            ->bulkToggleable(),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['category_ids'] ?? null,
                        fn (Builder $query, array $categoryIds): Builder => $query->whereHas('categories', fn (Builder $query) => $query->whereIn('categories.id', $categoryIds)
                        )
                    )),

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
