<?php

declare(strict_types=1);

namespace App\Livewire\Area;

use App\Models\Area;
use App\Models\Plant;
use App\Services\AreaService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

final class PlantSelectionModal extends Component
{
    public Area $area;

    public bool $showModal = false;

    public string $search = '';

    public ?int $selectedPlantTypeId = null;

    public array $selectedPlants = [];

    public function mount(Area $area): void
    {
        $this->area = $area;
    }

    public function openModal(): void
    {
        $this->showModal = true;
        $this->reset(['search', 'selectedPlantTypeId', 'selectedPlants']);
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['search', 'selectedPlantTypeId', 'selectedPlants']);
    }

    public function togglePlant(int $plantId): void
    {
        if (isset($this->selectedPlants[$plantId])) {
            unset($this->selectedPlants[$plantId]);
        } else {
            $this->selectedPlants[$plantId] = [
                'quantity' => 1,
                'notes' => '',
            ];
        }
    }

    public function updateQuantity(int $plantId, int $quantity): void
    {
        if (isset($this->selectedPlants[$plantId]) && $quantity >= 1) {
            $this->selectedPlants[$plantId]['quantity'] = $quantity;
        }
    }

    public function updateNotes(int $plantId, string $notes): void
    {
        if (isset($this->selectedPlants[$plantId])) {
            $this->selectedPlants[$plantId]['notes'] = $notes;
        }
    }

    public function addSelectedPlants(): void
    {
        if ($this->selectedPlants === []) {
            return;
        }

        $plantData = [];
        foreach ($this->selectedPlants as $plantId => $data) {
            $plantData[$plantId] = [
                'quantity' => $data['quantity'],
                'notes' => $data['notes'],
                'planted_at' => now(),
            ];
        }

        $this->area->plants()->syncWithoutDetaching($plantData);

        $this->dispatch('plants-added');
        $this->closeModal();

        session()->flash('success', 'Pflanzen wurden erfolgreich hinzugefügt.');
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'selectedPlantTypeId']);
    }

    public function getAvailablePlantsProperty(): Collection
    {
        return app(AreaService::class)->getFilteredPlantsForArea(
            $this->area,
            $this->search,
            $this->selectedPlantTypeId
        );
    }

    public function getPlantTypeOptionsProperty(): array
    {
        return app(AreaService::class)->getPlantTypeOptions();
    }

    public function getSelectedPlantsDataProperty(): Collection
    {
        if ($this->selectedPlants === []) {
            return collect();
        }

        return Plant::with(['plantType'])
            ->whereIn('id', array_keys($this->selectedPlants))
            ->get();
    }

    public function getHasActiveFiltersProperty(): bool
    {
        return ($this->search !== '' && $this->search !== '0') || $this->selectedPlantTypeId !== null;
    }

    public function getSelectedPlantsCountProperty(): int
    {
        return count($this->selectedPlants);
    }

    public function render(): View
    {
        return view('livewire.area.plant-selection-modal');
    }
}
