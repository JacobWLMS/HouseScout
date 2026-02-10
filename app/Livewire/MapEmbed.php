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

    public function satelliteMapUrl(): string
    {
        $apiKey = config('housescout.api.google_maps_embed.key');
        $params = [
            'key' => $apiKey,
            'q' => $this->latitude.','.$this->longitude,
            'maptype' => 'satellite',
            'zoom' => '18',
        ];

        return 'https://www.google.com/maps/embed/v1/place?'.http_build_query($params);
    }

    public function streetViewUrl(): string
    {
        $apiKey = config('housescout.api.google_maps_embed.key');
        $params = [
            'key' => $apiKey,
            'location' => $this->latitude.','.$this->longitude,
            'heading' => '0',
            'pitch' => '0',
        ];

        return 'https://www.google.com/maps/embed/v1/streetview?'.http_build_query($params);
    }

    public function render(): View
    {
        return view('livewire.map-embed');
    }
}
