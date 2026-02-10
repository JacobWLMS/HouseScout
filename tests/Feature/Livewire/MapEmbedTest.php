<?php

use App\Livewire\MapEmbed;
use App\Models\Property;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
    config()->set('housescout.api.google_maps_embed.key', 'test-api-key');
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

test('map embed renders google maps embed iframes', function () {
    $property = Property::factory()->create([
        'latitude' => 51.5034,
        'longitude' => -0.1276,
    ]);

    Livewire::test(MapEmbed::class, ['property' => $property])
        ->assertSeeHtml('google.com/maps/embed/v1/place')
        ->assertSeeHtml('google.com/maps/embed/v1/streetview');
});

test('map embed uses api key from config', function () {
    $property = Property::factory()->create([
        'latitude' => 51.5034,
        'longitude' => -0.1276,
    ]);

    Livewire::test(MapEmbed::class, ['property' => $property])
        ->assertSeeHtml('test-api-key');
});

test('map embed iframes have proper attributes', function () {
    $property = Property::factory()->create([
        'latitude' => 51.5034,
        'longitude' => -0.1276,
    ]);

    Livewire::test(MapEmbed::class, ['property' => $property])
        ->assertSeeHtml('allowfullscreen')
        ->assertSeeHtml('loading="lazy"');
});

test('map embed includes coordinates in iframe urls', function () {
    $property = Property::factory()->create([
        'latitude' => 51.5034,
        'longitude' => -0.1276,
    ]);

    Livewire::test(MapEmbed::class, ['property' => $property])
        ->assertSeeHtml('51.5034')
        ->assertSeeHtml('-0.1276');
});
