<?php

declare(strict_types=1);

use App\Livewire\Area\PlantSelectionModal;
use App\Models\Area;
use App\Models\Garden;
use App\Models\Plant;
use App\Models\PlantType;
use App\Models\User;
use Livewire\Livewire;

describe('PlantSelectionModal', function () {
    it('can render the component', function () {
        $user = User::factory()->create();
        $garden = Garden::factory()->for($user)->create();
        $area = Area::factory()->for($garden)->create();

        $this->actingAs($user);

        Livewire::test(PlantSelectionModal::class, ['area' => $area])
            ->assertStatus(200)
            ->assertSee('Pflanzen hinzufügen');
    });

    it('shows available plants excluding already planted ones', function () {
        $user = User::factory()->create();
        $garden = Garden::factory()->for($user)->create();
        $area = Area::factory()->for($garden)->create();
        $plantType = PlantType::factory()->state(['name' => 'vegetable'])->create();

        $availablePlant = Plant::factory()->for($plantType)->create(['name' => 'Available Plant']);
        $plantedPlant = Plant::factory()->for($plantType)->create(['name' => 'Planted Plant']);

        $area->plants()->attach($plantedPlant, ['quantity' => 1, 'planted_at' => now()]);

        $this->actingAs($user);

        Livewire::test(PlantSelectionModal::class, ['area' => $area])
            ->call('openModal')
            ->assertSee('Available Plant')
            ->assertDontSee('Planted Plant');
    });

    it('can search plants by name', function () {
        $user = User::factory()->create();
        $garden = Garden::factory()->for($user)->create();
        $area = Area::factory()->for($garden)->create();
        $plantType = PlantType::factory()->state(['name' => 'herb'])->create();

        Plant::factory()->for($plantType)->create(['name' => 'Tomato']);
        Plant::factory()->for($plantType)->create(['name' => 'Cucumber']);

        $this->actingAs($user);

        Livewire::test(PlantSelectionModal::class, ['area' => $area])
            ->call('openModal')
            ->set('search', 'Tomato')
            ->assertSee('Tomato')
            ->assertDontSee('Cucumber');
    });

    it('can filter plants by type', function () {
        $user = User::factory()->create();
        $garden = Garden::factory()->for($user)->create();
        $area = Area::factory()->for($garden)->create();

        $vegetableType = PlantType::factory()->state(['name' => 'tree'])->create();
        $flowerType = PlantType::factory()->state(['name' => 'shrub'])->create();

        Plant::factory()->for($vegetableType)->create(['name' => 'Tomato']);
        Plant::factory()->for($flowerType)->create(['name' => 'Rose']);

        $this->actingAs($user);

        Livewire::test(PlantSelectionModal::class, ['area' => $area])
            ->call('openModal')
            ->set('selectedPlantTypeId', $vegetableType->id)
            ->assertSee('Tomato')
            ->assertDontSee('Rose');
    });

    it('can select and deselect plants', function () {
        $user = User::factory()->create();
        $garden = Garden::factory()->for($user)->create();
        $area = Area::factory()->for($garden)->create();
        $plantType = PlantType::factory()->state(['name' => 'fruit'])->create();
        $plant = Plant::factory()->for($plantType)->create();

        $this->actingAs($user);

        $component = Livewire::test(PlantSelectionModal::class, ['area' => $area])
            ->call('openModal')
            ->call('togglePlant', $plant->id);

        expect($component->get('selectedPlants'))->toHaveKey($plant->id);

        $component->call('togglePlant', $plant->id);

        expect($component->get('selectedPlants'))->not->toHaveKey($plant->id);
    });

    it('can update quantity for selected plants', function () {
        $user = User::factory()->create();
        $garden = Garden::factory()->for($user)->create();
        $area = Area::factory()->for($garden)->create();
        $plantType = PlantType::factory()->state(['name' => 'grass'])->create();
        $plant = Plant::factory()->for($plantType)->create();

        $this->actingAs($user);

        Livewire::test(PlantSelectionModal::class, ['area' => $area])
            ->call('openModal')
            ->call('togglePlant', $plant->id)
            ->call('updateQuantity', $plant->id, 5)
            ->assertSet('selectedPlants.'.$plant->id.'.quantity', 5);
    });

    it('can update notes for selected plants', function () {
        $user = User::factory()->create();
        $garden = Garden::factory()->for($user)->create();
        $area = Area::factory()->for($garden)->create();
        $plantType = PlantType::factory()->state(['name' => 'fern'])->create();
        $plant = Plant::factory()->for($plantType)->create();

        $this->actingAs($user);

        Livewire::test(PlantSelectionModal::class, ['area' => $area])
            ->call('openModal')
            ->call('togglePlant', $plant->id)
            ->call('updateNotes', $plant->id, 'Test notes')
            ->assertSet('selectedPlants.'.$plant->id.'.notes', 'Test notes');
    });

    it('can add selected plants to the area', function () {
        $user = User::factory()->create();
        $garden = Garden::factory()->for($user)->create();
        $area = Area::factory()->for($garden)->create();
        $plantType = PlantType::factory()->state(['name' => 'aquatic'])->create();
        $plant = Plant::factory()->for($plantType)->create();

        $this->actingAs($user);

        Livewire::test(PlantSelectionModal::class, ['area' => $area])
            ->call('openModal')
            ->call('togglePlant', $plant->id)
            ->call('updateQuantity', $plant->id, 3)
            ->call('updateNotes', $plant->id, 'Test location')
            ->call('addSelectedPlants');

        $area->refresh();

        expect($area->plants)->toHaveCount(1);
        expect($area->plants->first()->pivot->quantity)->toBe(3);
        expect($area->plants->first()->pivot->notes)->toBe('Test location');
    });

    it('closes modal after adding plants', function () {
        $user = User::factory()->create();
        $garden = Garden::factory()->for($user)->create();
        $area = Area::factory()->for($garden)->create();
        $plantType = PlantType::factory()->state(['name' => 'flower'])->create();
        $plant = Plant::factory()->for($plantType)->create();

        $this->actingAs($user);

        Livewire::test(PlantSelectionModal::class, ['area' => $area])
            ->call('openModal')
            ->assertSet('showModal', true)
            ->call('togglePlant', $plant->id)
            ->call('addSelectedPlants')
            ->assertSet('showModal', false);
    });

    it('can clear filters', function () {
        $user = User::factory()->create();
        $garden = Garden::factory()->for($user)->create();
        $area = Area::factory()->for($garden)->create();
        $plantType = PlantType::factory()->state(['name' => 'succulent'])->create();

        $this->actingAs($user);

        Livewire::test(PlantSelectionModal::class, ['area' => $area])
            ->call('openModal')
            ->set('search', 'test search')
            ->set('selectedPlantTypeId', $plantType->id)
            ->call('clearFilters')
            ->assertSet('search', '')
            ->assertSet('selectedPlantTypeId', null);
    });

    it('does not add plants if none are selected', function () {
        $user = User::factory()->create();
        $garden = Garden::factory()->for($user)->create();
        $area = Area::factory()->for($garden)->create();

        $this->actingAs($user);

        Livewire::test(PlantSelectionModal::class, ['area' => $area])
            ->call('openModal')
            ->call('addSelectedPlants');

        $area->refresh();
        expect($area->plants)->toHaveCount(0);
    });
});
