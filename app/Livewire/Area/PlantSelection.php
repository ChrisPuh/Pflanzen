<?php

declare(strict_types=1);

namespace App\Livewire\Area;

use App\DTOs\Area\Actions\AttachPlantToAreaDTO;
use App\Models\Area;
use App\Models\Plant;
use App\Services\Area\AreaPlantService;
use App\Services\Area\AreaService;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

final class PlantSelection extends Component
{
    public Area $area;

    public string $search = '';

    public ?int $selectedPlantTypeId = null;

    public array $selectedPlants = [];

    public function rules(): array
    {
        return [
            'selectedPlants' => ['required', 'array', 'min:1'],
            'selectedPlants.*' => ['required', 'array'],
            'selectedPlants.*.quantity' => ['required', 'integer', 'min:1', 'max:9999'],
            'selectedPlants.*.notes' => ['nullable', 'string', 'max:500'],
            'selectedPlants.*.planted_at' => ['date'],
            'selectedPlants.*.plant_id' => ['required', 'integer', 'exists:plants,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'selectedPlants.required' => 'Bitte wählen Sie mindestens eine Pflanze aus.',
            'selectedPlants.array' => 'Ungültige Pflanzenauswahl.',
            'selectedPlants.min' => 'Bitte wählen Sie mindestens eine Pflanze aus.',
            'selectedPlants.*.quantity.required' => 'Bitte geben Sie eine Anzahl an.',
            'selectedPlants.*.quantity.integer' => 'Die Anzahl muss eine ganze Zahl sein.',
            'selectedPlants.*.quantity.min' => 'Die Anzahl muss mindestens 1 sein.',
            'selectedPlants.*.quantity.max' => 'Die Anzahl darf maximal 9999 sein.',
            'selectedPlants.*.notes.max' => 'Die Notizen dürfen maximal 500 Zeichen lang sein.',
            'selectedPlants.*.planted_at.date' => 'Das Pflanzdatum ist ungültig.',
            'selectedPlants.*.plant_id.required' => 'Bitte wählen Sie eine Pflanze aus.',
            'selectedPlants.*.plant_id.integer' => 'Ungültige Pflanzen-ID.',
            'selectedPlants.*.plant_id.exists' => 'Die ausgewählte Pflanze existiert nicht.',
        ];
    }

    public function mount(Area $area): void
    {
        $this->area = $area;
    }

    public function resetSelection(): void
    {
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
                'planted_at' => now(),
                'plant_id' => $plantId,
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

    public function addSelectedPlants(AreaPlantService $service): void
    {
        if ($this->selectedPlants === []) {
            return;
        }

        $plantData = [];
        foreach ($this->selectedPlants as $plantId => $data) {
            $plantData[$plantId] = [
                'quantity' => $data['quantity'],
                'notes' => $data['notes'],
                'planted_at' => $data['planted_at'] ?? now(),
                'plant_id' => $plantId,
            ];
        }

        try {
            $service->attachPlantsToArea($this->area, AttachPlantToAreaDTO::fromValidatedRequest($plantData));
            session()->flash('success', 'Pflanzen wurden erfolgreich hinzugefügt.');
            $this->dispatch('plants-added');
        } catch (Exception $exception) {
            session()->flash('error', 'Fehler beim Hinzufügen der Pflanzen: '.$exception->getMessage());
        }

        $this->resetSelection();
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
        return view('livewire.area.plant-selection');
    }
}
