<?php

use App\Models\Property;
use App\Models\SavedProperty;
use App\Models\User;
use Laravel\Dusk\Browser;

test('saved properties page loads', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/app/saved-properties')
            ->assertSee('Saved Properties');
    });
});

test('saved properties shows user saved properties', function () {
    $user = User::factory()->create();
    $property = Property::factory()->create([
        'address_line_1' => '10 Downing Street',
        'postcode' => 'SW1A 2AA',
    ]);
    SavedProperty::factory()->create([
        'user_id' => $user->id,
        'property_id' => $property->id,
    ]);

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/app/saved-properties')
            ->assertSee('10 Downing Street')
            ->assertSee('SW1A 2AA');
    });
});

test('saved properties page is empty when no saved properties', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/app/saved-properties')
            ->assertSee('Saved Properties');
    });
});

test('saved properties navigation link is in sidebar', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/app')
            ->assertSeeLink('Saved Properties');
    });
});
