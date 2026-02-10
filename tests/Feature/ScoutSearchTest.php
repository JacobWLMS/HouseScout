<?php

use App\Models\Property;

test('property is searchable', function () {
    $property = Property::factory()->create([
        'address_line_1' => '10 Downing Street',
        'postcode' => 'SW1A 2AA',
        'city' => 'Westminster',
    ]);

    $results = Property::search('Downing')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->id)->toBe($property->id);
});

test('property search by postcode', function () {
    Property::factory()->create(['postcode' => 'SW1A 2AA', 'address_line_1' => '10 Downing St']);
    Property::factory()->create(['postcode' => 'EC1A 1BB', 'address_line_1' => '1 London Wall']);

    $results = Property::search('SW1A')->get();

    expect($results)->toHaveCount(1);
});

test('property search by city', function () {
    Property::factory()->create(['city' => 'Westminster', 'address_line_1' => '10 Downing St', 'postcode' => 'SW1A 2AA']);
    Property::factory()->create(['city' => 'Camden', 'address_line_1' => '1 Camden High St', 'postcode' => 'NW1 0JH']);

    $results = Property::search('Westminster')->get();

    expect($results)->toHaveCount(1);
});

test('property search returns empty for no matches', function () {
    Property::factory()->create(['address_line_1' => '10 Downing St', 'postcode' => 'SW1A 2AA']);

    $results = Property::search('nonexistent')->get();

    expect($results)->toHaveCount(0);
});
