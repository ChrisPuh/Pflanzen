<?php

declare(strict_types=1);

namespace App\Livewire\Area;

use App\Models\Area;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class PlantSelectionModal extends Component
{
    public Area $area;

    public bool $showModal = false;

    public function mount(Area $area): void
    {
        $this->area = $area;
    }

    public function openModal(): void
    {
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function render(): View
    {
        return view('livewire.area.plant-selection-modal');
    }

    protected function getListeners(): array
    {
        return [
            'plants-added' => 'closeModal',
        ];
    }
}
