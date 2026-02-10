<?php

use App\Filament\Widgets\StatsOverviewWidget;
use App\Models\Property;
use App\Models\PropertySearch;
use App\Models\SavedProperty;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('stats overview widget renders on dashboard', function () {
    $response = $this->get('/app');

    $response->assertStatus(200);
    $response->assertSeeLivewire(StatsOverviewWidget::class);
});

test('widget shows total searches count for user', function () {
    $properties = Property::factory()->count(3)->create();

    foreach ($properties as $property) {
        PropertySearch::factory()->create([
            'user_id' => $this->user->id,
            'property_id' => $property->id,
            'searched_at' => now(),
        ]);
    }

    Livewire::test(StatsOverviewWidget::class)
        ->assertSee('Total Searches')
        ->assertSee('3');
});

test('widget shows saved properties count for user', function () {
    SavedProperty::factory()->count(2)->create([
        'user_id' => $this->user->id,
    ]);

    Livewire::test(StatsOverviewWidget::class)
        ->assertSee('Saved Properties')
        ->assertSee('2');
});

test('widget shows most searched postcode area', function () {
    $property1 = Property::factory()->create(['postcode' => 'SW1A 1AA']);
    $property2 = Property::factory()->create(['postcode' => 'SW1A 2PW']);
    $property3 = Property::factory()->create(['postcode' => 'EC2R 8AH']);

    // SW1A searched 3 times, EC2R searched 1 time
    PropertySearch::factory()->count(2)->create([
        'user_id' => $this->user->id,
        'property_id' => $property1->id,
        'searched_at' => now(),
    ]);
    PropertySearch::factory()->create([
        'user_id' => $this->user->id,
        'property_id' => $property2->id,
        'searched_at' => now(),
    ]);
    PropertySearch::factory()->create([
        'user_id' => $this->user->id,
        'property_id' => $property3->id,
        'searched_at' => now(),
    ]);

    Livewire::test(StatsOverviewWidget::class)
        ->assertSee('Top Postcode Area')
        ->assertSee('SW1A');
});

test('widget shows N/A when no searches exist', function () {
    Livewire::test(StatsOverviewWidget::class)
        ->assertSee('N/A')
        ->assertSee('0');
});

test('different users see different stats', function () {
    $otherUser = User::factory()->create();

    // Create searches for the other user
    $property = Property::factory()->create();
    PropertySearch::factory()->count(5)->create([
        'user_id' => $otherUser->id,
        'property_id' => $property->id,
        'searched_at' => now(),
    ]);
    SavedProperty::factory()->count(3)->create([
        'user_id' => $otherUser->id,
    ]);

    // Current user should see 0 searches and 0 saved
    Livewire::test(StatsOverviewWidget::class)
        ->assertSee('Total Searches')
        ->assertSee('0')
        ->assertSee('Saved Properties');
});
