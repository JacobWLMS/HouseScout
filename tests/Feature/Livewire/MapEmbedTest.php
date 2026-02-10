<?php

use App\Livewire\MapEmbed;
use App\Models\Property;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('map embed component renders', function () {
    $property = Property::factory()->create([
        'latitude' => 51.5034,
        'longitude' => -0.1276,
    ]);

    Livewire::test(MapEmbed::class, ['property' => $property])
        ->assertSee('Location')
        ->assertSee('Satellite')
        ->assertSee('Street View');
});

test('map embed sets coordinates from property', function () {
    $property = Property::factory()->create([
        'latitude' => 51.5034,
        'longitude' => -0.1276,
    ]);

    Livewire::test(MapEmbed::class, ['property' => $property])
        ->assertSet('latitude', 51.5034)
        ->assertSet('longitude', -0.1276);
});

test('map embed uses default coordinates when property has none', function () {
    $property = Property::factory()->create([
        'latitude' => null,
        'longitude' => null,
    ]);

    Livewire::test(MapEmbed::class, ['property' => $property])
        ->assertSet('latitude', 51.5074)
        ->assertSet('longitude', -0.1278);
});
