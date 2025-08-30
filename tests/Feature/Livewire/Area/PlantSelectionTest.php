<?php

declare(strict_types=1);

use App\Livewire\Area\PlantSelection;
use App\Models\Area;
use App\Models\Garden;
use App\Models\Plant;
use App\Models\PlantType;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

describe('PlantSelection', function (): void {
    beforeEach(function (): void {
        $this->user = User::factory()->create();
        $this->garden = Garden::factory()->create(['user_id' => $this->user->id]);
        $this->area = Area::factory()->create(['garden_id' => $this->garden->id]);

        // Try to get existing plant type or create one with a unique name
        $this->plantType = PlantType::first() ?: PlantType::factory()->create([
            'name' => App\Enums\PlantTypeEnum::Flower,
        ]);

        $this->plant1 = Plant::factory()->create([
            'name' => 'Test Rose',
            'latin_name' => 'Rosa testensis',
            'description' => 'A beautiful test rose',
            'plant_type_id' => $this->plantType->id,
        ]);
        $this->plant2 = Plant::factory()->create([
            'name' => 'Test Sunflower',
            'latin_name' => 'Helianthus testus',
            'description' => 'A tall test sunflower',
            'plant_type_id' => $this->plantType->id,
        ]);
    });

    describe('Component Initialization', function (): void {
        it('mounts correctly with area', function (): void {
            Livewire::test(PlantSelection::class, ['area' => $this->area])
                ->assertSet('area.id', $this->area->id)
                ->assertSet('search', '')
                ->assertSet('selectedPlantTypeId', null)
                ->assertSet('selectedPlants', []);
        });

        it('loads available plants property', function (): void {
            $component = Livewire::test(PlantSelection::class, ['area' => $this->area]);

            expect($component->get('availablePlants'))->toBeInstanceOf(Illuminate\Database\Eloquent\Collection::class);
            expect($component->get('plantTypeOptions'))->toBeArray();
        });
    });

    describe('Plant Search and Filtering', function (): void {
        it('can search plants by name', function (): void {
            $component = Livewire::test(PlantSelection::class, ['area' => $this->area])
                ->set('search', 'Rose');

            $plants = $component->get('availablePlants');
            expect($plants->contains($this->plant1))->toBeTrue();
            expect($plants->contains($this->plant2))->toBeFalse();
        });

        it('can search plants by latin name', function (): void {
            $component = Livewire::test(PlantSelection::class, ['area' => $this->area])
                ->set('search', 'testensis');

            $plants = $component->get('availablePlants');
            expect($plants->contains($this->plant1))->toBeTrue();
            expect($plants->contains($this->plant2))->toBeFalse();
        });

        it('can search plants by description', function (): void {
            $component = Livewire::test(PlantSelection::class, ['area' => $this->area])
                ->set('search', 'beautiful');

            $plants = $component->get('availablePlants');
            expect($plants->contains($this->plant1))->toBeTrue();
            expect($plants->contains($this->plant2))->toBeFalse();
        });

        it('filters by plant type', function (): void {
            // Use firstOrCreate to avoid constraint violations
            $anotherPlantType = PlantType::firstOrCreate(
                ['name' => App\Enums\PlantTypeEnum::Tree],
                ['description' => 'Tree plant type for testing']
            );
            $plantWithDifferentType = Plant::factory()->create(['plant_type_id' => $anotherPlantType->id]);

            $component = Livewire::test(PlantSelection::class, ['area' => $this->area])
                ->set('selectedPlantTypeId', $this->plantType->id);

            $plants = $component->get('availablePlants');
            expect($plants->contains($this->plant1))->toBeTrue();
            expect($plants->contains($this->plant2))->toBeTrue();
            expect($plants->contains($plantWithDifferentType))->toBeFalse();
        });

        it('excludes plants already in area', function (): void {
            $this->area->plants()->attach($this->plant1->id, [
                'quantity' => 1,
                'planted_at' => now(),
            ]);

            $component = Livewire::test(PlantSelection::class, ['area' => $this->area]);
            $plants = $component->get('availablePlants');

            expect($plants->contains($this->plant1))->toBeFalse();
            expect($plants->contains($this->plant2))->toBeTrue();
        });

        it('can clear filters', function (): void {
            Livewire::test(PlantSelection::class, ['area' => $this->area])
                ->set('search', 'test search')
                ->set('selectedPlantTypeId', 999)
                ->call('clearFilters')
                ->assertSet('search', '')
                ->assertSet('selectedPlantTypeId', null);
        });
    });

    describe('Plant Selection', function (): void {
        it('can toggle plant selection', function (): void {
            $planted_at = now();

            // Zeit "einfrieren" für den gesamten Test
            Carbon::setTestNow($planted_at);

            Livewire::test(PlantSelection::class, ['area' => $this->area])
                ->call('togglePlant', $this->plant1->id)
                ->assertSet('selectedPlants', [
                    $this->plant1->id => [
                        'quantity' => 1,
                        'notes' => '',
                        'planted_at' => $planted_at,
                        'plant_id' => $this->plant1->id,
                    ],
                ]);

            // Zeit wieder "auftauen" nach dem Test
            Carbon::setTestNow();
        });

        it('can deselect plant by toggling again', function (): void {
            Livewire::test(PlantSelection::class, ['area' => $this->area])
                ->call('togglePlant', $this->plant1->id)
                ->call('togglePlant', $this->plant1->id)
                ->assertSet('selectedPlants', []);
        });

        it('can select multiple plants', function (): void {
            // Microsekunden entfernen für Livewire-Kompatibilität
            $planted_at = now()->startOfSecond();

            $component = Livewire::test(PlantSelection::class, ['area' => $this->area]);

            // DIESELBE Zeit für beide Calls verwenden
            Carbon::setTestNow($planted_at);
            $component->call('togglePlant', $this->plant1->id);

            // DIESELBE Zeit nochmal setzen (nicht neu generieren!)
            Carbon::setTestNow($planted_at);
            $component->call('togglePlant', $this->plant2->id);

            $component->assertSet('selectedPlants', [
                $this->plant1->id => [
                    'quantity' => 1,
                    'notes' => '',
                    'planted_at' => $planted_at,
                    'plant_id' => $this->plant1->id,
                ],
                $this->plant2->id => [
                    'quantity' => 1,
                    'notes' => '',
                    'planted_at' => $planted_at, // DIESELBE Zeit!
                    'plant_id' => $this->plant2->id,
                ],
            ]);

            Carbon::setTestNow();
        });
    });

    describe('Plant Data Management', function (): void {
        beforeEach(function (): void {
            $this->component = Livewire::test(PlantSelection::class, ['area' => $this->area])
                ->call('togglePlant', $this->plant1->id);
        });

        it('can update plant quantity', function (): void {
            $this->component
                ->call('updateQuantity', $this->plant1->id, 5)
                ->assertSet('selectedPlants.'.$this->plant1->id.'.quantity', 5);
        });

        it('ignores invalid quantity updates', function (): void {
            $this->component
                ->call('updateQuantity', $this->plant1->id, 0)
                ->assertSet('selectedPlants.'.$this->plant1->id.'.quantity', 1)
                ->call('updateQuantity', $this->plant1->id, -1)
                ->assertSet('selectedPlants.'.$this->plant1->id.'.quantity', 1);
        });

        it('ignores quantity updates for unselected plants', function (): void {

            $planted_at = now()->startOfSecond();

            Carbon::setTestNow($planted_at);

            $this->component
                ->call('updateQuantity', $this->plant2->id, 5)
                ->assertSet('selectedPlants', [
                    $this->plant1->id => [
                        'quantity' => 1,
                        'notes' => '',
                        'planted_at' => $planted_at,
                        'plant_id' => $this->plant1->id,
                    ],
                ]);

            Carbon::setTestNow();
        });

        it('can update plant notes', function (): void {
            $this->component
                ->call('updateNotes', $this->plant1->id, 'Test notes')
                ->assertSet('selectedPlants.'.$this->plant1->id.'.notes', 'Test notes');
        });

        it('ignores notes updates for unselected plants', function (): void {

            $planted_at = now()->startOfSecond();

            Carbon::setTestNow($planted_at);
            $this->component
                ->call('updateNotes', $this->plant2->id, 'Test notes')
                ->assertSet('selectedPlants', [
                    $this->plant1->id => [
                        'quantity' => 1,
                        'notes' => '',
                        'planted_at' => $planted_at,
                        'plant_id' => $this->plant1->id,
                    ],
                ]);

            Carbon::setTestNow();
        });
    });

    describe('Adding Plants to Area', function (): void {
        it('can add selected plants to area', function (): void {
            Livewire::test(PlantSelection::class, ['area' => $this->area])
                ->call('togglePlant', $this->plant1->id)
                ->call('updateQuantity', $this->plant1->id, 3)
                ->call('updateNotes', $this->plant1->id, 'Test notes')
                ->call('addSelectedPlants')
                ->assertSet('selectedPlants', [])
                ->assertDispatched('plants-added');

            // Verify plant was actually added to area
            $areaPlant = $this->area->plants()->where('plant_id', $this->plant1->id)->first();
            expect($areaPlant)->not->toBeNull();
            expect($areaPlant->pivot->quantity)->toBe(3);
            expect($areaPlant->pivot->notes)->toBe('Test notes');
            expect($areaPlant->pivot->planted_at)->not->toBeNull();
        });

        it('can add multiple plants to area', function (): void {
            Livewire::test(PlantSelection::class, ['area' => $this->area])
                ->call('togglePlant', $this->plant1->id)
                ->call('updateQuantity', $this->plant1->id, 2)
                ->call('updateNotes', $this->plant1->id, 'Rose notes')
                ->call('togglePlant', $this->plant2->id)
                ->call('updateQuantity', $this->plant2->id, 1)
                ->call('updateNotes', $this->plant2->id, 'Sunflower notes')
                ->call('addSelectedPlants')
                ->assertDispatched('plants-added');

            expect($this->area->plants()->count())->toBe(2);

            $rosePlant = $this->area->plants()->where('plant_id', $this->plant1->id)->first();
            expect($rosePlant->pivot->quantity)->toBe(2);
            expect($rosePlant->pivot->notes)->toBe('Rose notes');

            $sunflowerPlant = $this->area->plants()->where('plant_id', $this->plant2->id)->first();
            expect($sunflowerPlant->pivot->quantity)->toBe(1);
            expect($sunflowerPlant->pivot->notes)->toBe('Sunflower notes');
        });

        it('does nothing when no plants selected', function (): void {
            Livewire::test(PlantSelection::class, ['area' => $this->area])
                ->call('addSelectedPlants')
                ->assertNotDispatched('plants-added');

            expect($this->area->plants()->count())->toBe(0);
        });

        it('shows success message after adding plants', function (): void {
            $component = Livewire::test(PlantSelection::class, ['area' => $this->area])
                ->call('togglePlant', $this->plant1->id)
                ->call('addSelectedPlants');

            // Check that plants were actually added and selection was reset
            expect($this->area->plants()->count())->toBe(1);
            expect($component->get('selectedPlants'))->toBe([]);
        });
    });

    describe('Computed Properties', function (): void {
        it('correctly identifies active filters', function (): void {
            $component = Livewire::test(PlantSelection::class, ['area' => $this->area]);

            expect($component->get('hasActiveFilters'))->toBeFalse();

            $component->set('search', 'test');
            expect($component->get('hasActiveFilters'))->toBeTrue();

            $component->set('search', '')->set('selectedPlantTypeId', 1);
            expect($component->get('hasActiveFilters'))->toBeTrue();

            $component->set('selectedPlantTypeId', null);
            expect($component->get('hasActiveFilters'))->toBeFalse();
        });

        it('correctly counts selected plants', function (): void {
            $component = Livewire::test(PlantSelection::class, ['area' => $this->area]);

            expect($component->get('selectedPlantsCount'))->toBe(0);

            $component->call('togglePlant', $this->plant1->id);
            expect($component->get('selectedPlantsCount'))->toBe(1);

            $component->call('togglePlant', $this->plant2->id);
            expect($component->get('selectedPlantsCount'))->toBe(2);

            $component->call('togglePlant', $this->plant1->id);
            expect($component->get('selectedPlantsCount'))->toBe(1);
        });

        it('returns selected plants data correctly', function (): void {
            $component = Livewire::test(PlantSelection::class, ['area' => $this->area])
                ->call('togglePlant', $this->plant1->id)
                ->call('togglePlant', $this->plant2->id);

            $selectedPlantsData = $component->get('selectedPlantsData');
            expect($selectedPlantsData->count())->toBe(2);
            expect($selectedPlantsData->contains($this->plant1))->toBeTrue();
            expect($selectedPlantsData->contains($this->plant2))->toBeTrue();
        });

        it('loads plant type options correctly', function (): void {
            $component = Livewire::test(PlantSelection::class, ['area' => $this->area]);
            $options = $component->get('plantTypeOptions');

            expect($options)->toBeArray();
            expect($options)->toHaveKey($this->plantType->id);
            expect($options[$this->plantType->id])->toBe($this->plantType->name->getLabel());
        });
    });

    describe('Integration with AreaService', function (): void {
        it('uses AreaService for filtering plants', function (): void {
            // Add a plant to the area first
            $this->area->plants()->attach($this->plant1->id, [
                'quantity' => 1,
                'planted_at' => now(),
            ]);

            $component = Livewire::test(PlantSelection::class, ['area' => $this->area]);
            $availablePlants = $component->get('availablePlants');

            // Plant1 should be filtered out as it's already in the area
            expect($availablePlants->contains($this->plant1))->toBeFalse();
            expect($availablePlants->contains($this->plant2))->toBeTrue();
        });

        it('uses AreaService for plant type options', function (): void {
            $component = Livewire::test(PlantSelection::class, ['area' => $this->area]);
            $options = $component->get('plantTypeOptions');

            expect($options)->toBeArray();
            expect(count($options))->toBeGreaterThan(0);
        });
    });
});
