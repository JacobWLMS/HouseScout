<?php

use App\Filament\Pages\ComparePropertiesPage;
use App\Models\ChecklistTemplate;
use App\Models\Property;
use App\Models\PropertyAssessment;
use App\Models\SavedProperty;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    ChecklistTemplate::factory()->dealBreaker()->create([
        'category' => 'flood_environmental',
        'category_label' => 'Flood & Environmental',
        'key' => 'flood_zone',
        'label' => 'Flood Zone',
        'sort_order' => 1,
    ]);
    ChecklistTemplate::factory()->important()->create([
        'category' => 'flood_environmental',
        'category_label' => 'Flood & Environmental',
        'key' => 'surface_water_risk',
        'label' => 'Surface Water Risk',
        'sort_order' => 2,
    ]);
    ChecklistTemplate::factory()->niceToHave()->create([
        'category' => 'neighbourhood',
        'category_label' => 'Neighbourhood',
        'key' => 'noise',
        'label' => 'Noise Levels',
        'sort_order' => 3,
    ]);
});

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

test('compare page shows comparison table with 2 properties', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $p1 = Property::factory()->create(['address_line_1' => '10 Downing Street']);
    $p2 = Property::factory()->create(['address_line_1' => '221B Baker Street']);

    SavedProperty::factory()->create(['user_id' => $user->id, 'property_id' => $p1->id]);
    SavedProperty::factory()->create(['user_id' => $user->id, 'property_id' => $p2->id]);

    Livewire::test(ComparePropertiesPage::class)
        ->assertSee('10 Downing Street')
        ->assertSee('221B Baker Street')
        ->assertSee('Weighted Score');
});

test('compare page limits to 6 properties', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    for ($i = 0; $i < 8; $i++) {
        $prop = Property::factory()->create(['address_line_1' => "Property {$i}"]);
        SavedProperty::factory()->create(['user_id' => $user->id, 'property_id' => $prop->id]);
    }

    $component = Livewire::test(ComparePropertiesPage::class);

    expect($component->get('properties'))->toHaveCount(6);
});

test('compare page sorts by score by default', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $p1 = Property::factory()->create(['address_line_1' => 'Low Score House']);
    $p2 = Property::factory()->create(['address_line_1' => 'High Score House']);

    $saved1 = SavedProperty::factory()->create(['user_id' => $user->id, 'property_id' => $p1->id]);
    $saved2 = SavedProperty::factory()->create(['user_id' => $user->id, 'property_id' => $p2->id]);

    // Give p2 all likes (higher score)
    PropertyAssessment::factory()->like()->create(['saved_property_id' => $saved2->id, 'item_key' => 'flood_zone']);
    PropertyAssessment::factory()->like()->create(['saved_property_id' => $saved2->id, 'item_key' => 'surface_water_risk']);
    PropertyAssessment::factory()->like()->create(['saved_property_id' => $saved2->id, 'item_key' => 'noise']);

    // Give p1 all dislikes (lower score)
    PropertyAssessment::factory()->dislike()->create(['saved_property_id' => $saved1->id, 'item_key' => 'flood_zone']);

    $component = Livewire::test(ComparePropertiesPage::class);

    $properties = $component->get('properties');
    expect($properties[0]['property']->address_line_1)->toBe('High Score House');
});

test('compare page sorts by name when selected', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $p1 = Property::factory()->create(['address_line_1' => 'Zebra Lane']);
    $p2 = Property::factory()->create(['address_line_1' => 'Apple Road']);

    SavedProperty::factory()->create(['user_id' => $user->id, 'property_id' => $p1->id]);
    SavedProperty::factory()->create(['user_id' => $user->id, 'property_id' => $p2->id]);

    $component = Livewire::test(ComparePropertiesPage::class)
        ->set('sortBy', 'name');

    $properties = $component->get('properties');
    expect($properties[0]['property']->address_line_1)->toBe('Apple Road');
    expect($properties[1]['property']->address_line_1)->toBe('Zebra Lane');
});

test('compare page filters differences when toggled', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $p1 = Property::factory()->create(['address_line_1' => 'House A']);
    $p2 = Property::factory()->create(['address_line_1' => 'House B']);

    $saved1 = SavedProperty::factory()->create(['user_id' => $user->id, 'property_id' => $p1->id]);
    $saved2 = SavedProperty::factory()->create(['user_id' => $user->id, 'property_id' => $p2->id]);

    // Same assessment for flood_zone (should be filtered out when differences only)
    PropertyAssessment::factory()->like()->create(['saved_property_id' => $saved1->id, 'item_key' => 'flood_zone']);
    PropertyAssessment::factory()->like()->create(['saved_property_id' => $saved2->id, 'item_key' => 'flood_zone']);

    // Different assessment for noise (should remain)
    PropertyAssessment::factory()->like()->create(['saved_property_id' => $saved1->id, 'item_key' => 'noise']);
    PropertyAssessment::factory()->dislike()->create(['saved_property_id' => $saved2->id, 'item_key' => 'noise']);

    $component = Livewire::test(ComparePropertiesPage::class)
        ->set('filterDifferences', true);

    $comparisonData = $component->call('getComparisonData');

    // Neighbourhood should be present (noise differs)
    $data = app()->call([$component->instance(), 'getComparisonData']);
    $allItems = $data->flatMap(fn ($cat) => $cat['items']->pluck('key'));
    expect($allItems)->toContain('noise');
    expect($allItems)->not->toContain('flood_zone');
});

test('compare page shows weighted score badges', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $p1 = Property::factory()->create(['address_line_1' => 'Score House']);
    $p2 = Property::factory()->create(['address_line_1' => 'Other House']);

    $saved1 = SavedProperty::factory()->create(['user_id' => $user->id, 'property_id' => $p1->id]);
    SavedProperty::factory()->create(['user_id' => $user->id, 'property_id' => $p2->id]);

    PropertyAssessment::factory()->like()->create(['saved_property_id' => $saved1->id, 'item_key' => 'flood_zone']);

    Livewire::test(ComparePropertiesPage::class)
        ->assertSee('Score House')
        ->assertSee('Weighted Score');
});

test('compare page shows recommendation text', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $p1 = Property::factory()->create(['address_line_1' => 'Best House']);
    $p2 = Property::factory()->create(['address_line_1' => 'Okay House']);

    $saved1 = SavedProperty::factory()->create(['user_id' => $user->id, 'property_id' => $p1->id]);
    SavedProperty::factory()->create(['user_id' => $user->id, 'property_id' => $p2->id]);

    PropertyAssessment::factory()->like()->create(['saved_property_id' => $saved1->id, 'item_key' => 'flood_zone']);
    PropertyAssessment::factory()->like()->create(['saved_property_id' => $saved1->id, 'item_key' => 'surface_water_risk']);

    Livewire::test(ComparePropertiesPage::class)
        ->assertSee('Best House scores highest');
});

test('compare page shows deal-breaker warning in recommendation', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $p1 = Property::factory()->create(['address_line_1' => 'Deal Breaker House']);
    $p2 = Property::factory()->create(['address_line_1' => 'Clean House']);

    $saved1 = SavedProperty::factory()->create(['user_id' => $user->id, 'property_id' => $p1->id]);
    $saved2 = SavedProperty::factory()->create(['user_id' => $user->id, 'property_id' => $p2->id]);

    // p1 has a disliked deal-breaker
    PropertyAssessment::factory()->dislike()->create(['saved_property_id' => $saved1->id, 'item_key' => 'flood_zone']);

    // p2 has a like on the deal-breaker (higher score)
    PropertyAssessment::factory()->like()->create(['saved_property_id' => $saved2->id, 'item_key' => 'flood_zone']);
    PropertyAssessment::factory()->like()->create(['saved_property_id' => $saved2->id, 'item_key' => 'surface_water_risk']);

    Livewire::test(ComparePropertiesPage::class)
        ->assertSee('unresolved deal-breakers');
});

test('compare page shows color-coded verdict cells', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $p1 = Property::factory()->create();
    $p2 = Property::factory()->create();

    $saved1 = SavedProperty::factory()->create(['user_id' => $user->id, 'property_id' => $p1->id]);
    $saved2 = SavedProperty::factory()->create(['user_id' => $user->id, 'property_id' => $p2->id]);

    PropertyAssessment::factory()->like()->create(['saved_property_id' => $saved1->id, 'item_key' => 'flood_zone']);
    PropertyAssessment::factory()->dislike()->create(['saved_property_id' => $saved2->id, 'item_key' => 'flood_zone']);

    Livewire::test(ComparePropertiesPage::class)
        ->assertSeeHtml('bg-green-50')
        ->assertSeeHtml('bg-red-50');
});

test('compare page shows category headers', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $p1 = Property::factory()->create();
    $p2 = Property::factory()->create();

    SavedProperty::factory()->create(['user_id' => $user->id, 'property_id' => $p1->id]);
    SavedProperty::factory()->create(['user_id' => $user->id, 'property_id' => $p2->id]);

    Livewire::test(ComparePropertiesPage::class)
        ->assertSee('Flood & Environmental')
        ->assertSee('Neighbourhood');
});

test('compare page shows deal-breaker count badges', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $p1 = Property::factory()->create();
    $p2 = Property::factory()->create();

    $saved1 = SavedProperty::factory()->create(['user_id' => $user->id, 'property_id' => $p1->id]);
    SavedProperty::factory()->create(['user_id' => $user->id, 'property_id' => $p2->id]);

    PropertyAssessment::factory()->dislike()->create(['saved_property_id' => $saved1->id, 'item_key' => 'flood_zone']);

    Livewire::test(ComparePropertiesPage::class)
        ->assertSee('1 flagged')
        ->assertSee('None');
});

test('compare page shows sort by and filter controls', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $p1 = Property::factory()->create();
    $p2 = Property::factory()->create();

    SavedProperty::factory()->create(['user_id' => $user->id, 'property_id' => $p1->id]);
    SavedProperty::factory()->create(['user_id' => $user->id, 'property_id' => $p2->id]);

    Livewire::test(ComparePropertiesPage::class)
        ->assertSee('Sort by:')
        ->assertSee('Show differences only');
});
