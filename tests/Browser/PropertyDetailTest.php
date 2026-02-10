<?php

use App\Models\Property;
use App\Models\SavedProperty;
use App\Models\User;
use Laravel\Dusk\Browser;

test('property detail page shows property address', function () {
    $user = User::factory()->create();
    $property = Property::factory()->create([
        'address_line_1' => '10 Downing Street',
        'postcode' => 'SW1A 2AA',
    ]);

    $this->browse(function (Browser $browser) use ($user, $property) {
        $browser->loginAs($user)
            ->visit("/app/properties/{$property->id}")
            ->assertSee('10 Downing Street')
            ->assertSee('SW1A 2AA');
    });
});

test('property detail page has save button for unsaved property', function () {
    $user = User::factory()->create();
    $property = Property::factory()->create();

    $this->browse(function (Browser $browser) use ($user, $property) {
        $browser->loginAs($user)
            ->visit("/app/properties/{$property->id}")
            ->assertSee('Save Property');
    });
});

test('property detail page shows tabs', function () {
    $user = User::factory()->create();
    $property = Property::factory()->create();

    $this->browse(function (Browser $browser) use ($user, $property) {
        $browser->loginAs($user)
            ->visit("/app/properties/{$property->id}")
            ->assertSee('Overview')
            ->assertSee('EPC')
            ->assertSee('Planning')
            ->assertSee('Flood Risk')
            ->assertSee('Crime')
            ->assertSee('Land Registry');
    });
});

test('property detail page shows demand count', function () {
    $user = User::factory()->create();
    $property = Property::factory()->create();

    $this->browse(function (Browser $browser) use ($user, $property) {
        $browser->loginAs($user)
            ->visit("/app/properties/{$property->id}")
            ->assertSee('searched in last 30 days');
    });
});

test('property detail page shows unsave button for saved property', function () {
    $user = User::factory()->create();
    $property = Property::factory()->create();
    SavedProperty::factory()->create([
        'user_id' => $user->id,
        'property_id' => $property->id,
    ]);

    $this->browse(function (Browser $browser) use ($user, $property) {
        $browser->loginAs($user)
            ->visit("/app/properties/{$property->id}")
            ->assertSee('Unsave Property');
    });
});
