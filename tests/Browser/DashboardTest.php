<?php

use App\Models\Property;
use App\Models\PropertySearch;
use App\Models\User;
use Laravel\Dusk\Browser;

test('dashboard shows search widget', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/app')
            ->waitForText('Search for a Property')
            ->assertSee('Search for a Property');
    });
});

test('dashboard shows recent searches widget when searches exist', function () {
    $user = User::factory()->create();
    $property = Property::factory()->create();
    PropertySearch::factory()->create([
        'user_id' => $user->id,
        'property_id' => $property->id,
    ]);

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/app')
            ->waitForText('Recent Searches')
            ->assertSee('Recent Searches');
    });
});

test('dashboard shows stats overview', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/app')
            ->waitForText('Total Searches')
            ->assertSee('Total Searches')
            ->assertSee('Saved Properties');
    });
});

test('dashboard shows HouseScout branding', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/app')
            ->assertSee('HouseScout');
    });
});
