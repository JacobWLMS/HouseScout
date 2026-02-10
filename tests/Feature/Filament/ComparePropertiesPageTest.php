<?php

use App\Filament\Pages\ComparePropertiesPage;
use App\Models\Property;
use App\Models\SavedProperty;
use App\Models\User;
use Livewire\Livewire;

test('compare page renders for authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(ComparePropertiesPage::class)
        ->assertOk();
});

test('compare page shows message when fewer than 2 properties saved', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    SavedProperty::factory()->create(['user_id' => $user->id]);

    Livewire::test(ComparePropertiesPage::class)
        ->assertSee('Save at least 2 properties');
});

test('compare page shows comparison table with 2+ properties', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $p1 = Property::factory()->create(['address_line_1' => '10 Downing Street']);
    $p2 = Property::factory()->create(['address_line_1' => '221B Baker Street']);

    SavedProperty::factory()->create(['user_id' => $user->id, 'property_id' => $p1->id]);
    SavedProperty::factory()->create(['user_id' => $user->id, 'property_id' => $p2->id]);

    Livewire::test(ComparePropertiesPage::class)
        ->assertSee('10 Downing Street')
        ->assertSee('221B Baker Street')
        ->assertSee('Progress');
});
