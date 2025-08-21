<?php

declare(strict_types=1);

namespace App\Livewire\Area;

use App\Models\Area;
use App\Models\Plant;
use App\Models\PlantType;
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
        if (empty($this->selectedPlants)) {
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
        $query = Plant::with(['plantType', 'categories'])
            ->whereDoesntHave('areas', fn ($q) => $q->where('area_id', $this->area->id));

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('latin_name', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->selectedPlantTypeId) {
            $query->where('plant_type_id', $this->selectedPlantTypeId);
        }

        return $query->orderBy('name')->get();
    }

    public function getPlantTypesProperty(): Collection
    {
        return PlantType::orderBy('name')->get();
    }

    public function getPlantTypeOptionsProperty(): array
    {
        return $this->plantTypes->mapWithKeys(fn($type) => [$type->id => $type->name->getLabel()])->toArray();
    }

    public function render(): View
    {
        return view('livewire.area.plant-selection-modal');
    }
}
