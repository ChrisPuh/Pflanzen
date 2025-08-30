<?php

declare(strict_types=1);

use App\Livewire\Area\PlantSelectionModal;
use App\Models\Area;
use App\Models\Garden;
use App\Models\User;
use Livewire\Livewire;

describe('PlantSelectionModal', function (): void {
    beforeEach(function (): void {
        $this->user = User::factory()->create();
        $this->garden = Garden::factory()->create(['user_id' => $this->user->id]);
        $this->area = Area::factory()->create(['garden_id' => $this->garden->id]);
    });

    describe('Component Initialization', function (): void {
        it('mounts correctly with area', function (): void {
            Livewire::test(PlantSelectionModal::class, ['area' => $this->area])
                ->assertSet('area.id', $this->area->id)
                ->assertSet('showModal', false);
        });
    });

    describe('Modal Management', function (): void {
        it('can open modal', function (): void {
            Livewire::test(PlantSelectionModal::class, ['area' => $this->area])
                ->call('openModal')
                ->assertSet('showModal', true);
        });

        it('can close modal', function (): void {
            Livewire::test(PlantSelectionModal::class, ['area' => $this->area])
                ->set('showModal', true)
                ->call('closeModal')
                ->assertSet('showModal', false);
        });

        it('closes modal when plants-added event is received', function (): void {
            $component = Livewire::test(PlantSelectionModal::class, ['area' => $this->area])
                ->set('showModal', true);

            // Simulate the plants-added event from PlantSelection component
            $component->dispatch('plants-added');

            $component->assertSet('showModal', false);
        });
    });

    describe('View Integration', function (): void {
        it('renders correctly', function (): void {
            Livewire::test(PlantSelectionModal::class, ['area' => $this->area])
                ->assertSee('Pflanzen hinzufügen')
                ->assertSee($this->area->name);
        });

        it('shows modal when showModal is true', function (): void {
            $component = Livewire::test(PlantSelectionModal::class, ['area' => $this->area])
                ->set('showModal', true);

            // The modal should be visible in the view
            $component->assertSee('Wähle Pflanzen für');
        });
    });
});
