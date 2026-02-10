<?php

namespace App\Livewire;

use App\Models\Property;
use Illuminate\View\View;
use Livewire\Component;

class MapEmbed extends Component
{
    public float $latitude;

    public float $longitude;

    public string $address;

    public string $postcode;

    public function mount(Property $property): void
    {
        $this->latitude = (float) ($property->latitude ?? 51.5074);
        $this->longitude = (float) ($property->longitude ?? -0.1278);
        $this->address = $property->address_line_1.', '.$property->postcode;
        $this->postcode = $property->postcode;
    }

    public function render(): View
    {
        return view('livewire.map-embed');
    }
}
