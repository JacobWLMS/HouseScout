<?php

use App\Filament\Pages\PropertyDetailPage;
use App\Models\ChecklistTemplate;
use App\Models\CrimeData;
use App\Models\EpcData;
use App\Models\FloodRiskData;
use App\Models\LandRegistryData;
use App\Models\PlanningApplication;
use App\Models\Property;
use App\Models\PropertyAssessment;
use App\Models\PropertySearch;
use App\Models\SavedProperty;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('page heading shows address and postcode', function () {
    $property = Property::factory()->create([
        'address_line_1' => '10 Downing Street',
        'postcode' => 'SW1A 2AA',
    ]);

    Livewire::test(PropertyDetailPage::class, ['property' => $property])
        ->assertSee('10 Downing Street, SW1A 2AA');
});

test('page subheading shows city and county', function () {
    $property = Property::factory()->create([
        'address_line_2' => 'Westminster',
        'city' => 'London',
        'county' => 'Greater London',
    ]);

    Livewire::test(PropertyDetailPage::class, ['property' => $property])
        ->assertSee('Westminster, London, Greater London');
});

test('page subheading shows city only when county is null', function () {
    $property = Property::factory()->create([
        'address_line_2' => null,
        'city' => 'London',
        'county' => null,
    ]);

    Livewire::test(PropertyDetailPage::class, ['property' => $property])
        ->assertSee('London');
});

test('breadcrumbs include dashboard and postcode', function () {
    $property = Property::factory()->create(['postcode' => 'SW1A 2AA']);

    $component = Livewire::test(PropertyDetailPage::class, ['property' => $property]);

    $breadcrumbs = $component->instance()->getBreadcrumbs();

    expect($breadcrumbs)->toBe([
        '/' => 'Dashboard',
        '#' => 'SW1A 2AA',
    ]);
});

test('saveNotes without saved property shows warning notification', function () {
    $property = Property::factory()->create();

    Livewire::test(PropertyDetailPage::class, ['property' => $property])
        ->set('notes', 'Some notes')
        ->call('saveNotes')
        ->assertNotified('Save the property first to add notes');
});

test('saveNotes with saved property updates notes', function () {
    $property = Property::factory()->create();
    $saved = SavedProperty::factory()->create([
        'user_id' => $this->user->id,
        'property_id' => $property->id,
        'notes' => 'Old notes',
    ]);

    Livewire::test(PropertyDetailPage::class, ['property' => $property])
        ->set('notes', 'Updated notes')
        ->call('saveNotes')
        ->assertNotified('Notes saved');

    expect($saved->fresh()->notes)->toBe('Updated notes');
});

test('demand count is displayed correctly', function () {
    $property = Property::factory()->create();

    // Create 3 searches from different users within 30 days
    $users = User::factory()->count(3)->create();
    foreach ($users as $user) {
        PropertySearch::factory()->create([
            'user_id' => $user->id,
            'property_id' => $property->id,
            'searched_at' => now()->subDays(5),
        ]);
    }

    // Create 1 search older than 30 days (shouldn't count)
    PropertySearch::factory()->create([
        'user_id' => User::factory(),
        'property_id' => $property->id,
        'searched_at' => now()->subDays(45),
    ]);

    $component = Livewire::test(PropertyDetailPage::class, ['property' => $property]);

    expect($component->instance()->demandCount)->toBe(3);
});

test('page loads with all relationship data', function () {
    $property = Property::factory()->create();
    EpcData::factory()->create(['property_id' => $property->id]);
    FloodRiskData::factory()->create(['property_id' => $property->id]);
    LandRegistryData::factory()->create(['property_id' => $property->id]);
    PlanningApplication::factory()->create(['property_id' => $property->id]);
    CrimeData::factory()->create(['property_id' => $property->id]);

    $component = Livewire::test(PropertyDetailPage::class, ['property' => $property]);

    $loadedProperty = $component->instance()->property;

    expect($loadedProperty->relationLoaded('epcData'))->toBeTrue()
        ->and($loadedProperty->relationLoaded('floodRiskData'))->toBeTrue()
        ->and($loadedProperty->relationLoaded('landRegistryData'))->toBeTrue()
        ->and($loadedProperty->relationLoaded('planningApplications'))->toBeTrue()
        ->and($loadedProperty->relationLoaded('crimeData'))->toBeTrue()
        ->and($loadedProperty->epcData)->not->toBeNull()
        ->and($loadedProperty->floodRiskData)->not->toBeNull()
        ->and($loadedProperty->landRegistryData)->not->toBeNull()
        ->and($loadedProperty->planningApplications)->toHaveCount(1)
        ->and($loadedProperty->crimeData)->toHaveCount(1);
});

test('non-existent property returns 404', function () {
    $response = $this->get('/app/properties/99999');

    $response->assertStatus(404);
});

test('saved property notes are loaded on mount', function () {
    $property = Property::factory()->create();
    SavedProperty::factory()->create([
        'user_id' => $this->user->id,
        'property_id' => $property->id,
        'notes' => 'My property notes',
    ]);

    $component = Livewire::test(PropertyDetailPage::class, ['property' => $property]);

    expect($component->instance()->notes)->toBe('My property notes')
        ->and($component->instance()->savedProperty)->not->toBeNull();
});

test('unsaved property has empty notes on mount', function () {
    $property = Property::factory()->create();

    $component = Livewire::test(PropertyDetailPage::class, ['property' => $property]);

    expect($component->instance()->notes)->toBe('')
        ->and($component->instance()->savedProperty)->toBeNull();
});

test('loadChecklistData populates weightedScore and checklistGroups', function () {
    $property = Property::factory()->create();
    $saved = SavedProperty::factory()->create([
        'user_id' => $this->user->id,
        'property_id' => $property->id,
    ]);

    ChecklistTemplate::factory()->dealBreaker()->create([
        'category' => 'flood_environmental',
        'category_label' => 'Flood & Environmental',
        'key' => 'flood_zone',
        'label' => 'Flood Zone',
        'sort_order' => 1,
    ]);

    ChecklistTemplate::factory()->important()->create([
        'category' => 'energy_condition',
        'category_label' => 'Energy & Condition',
        'key' => 'epc_rating',
        'label' => 'EPC Rating',
        'sort_order' => 2,
    ]);

    PropertyAssessment::factory()->like()->create([
        'saved_property_id' => $saved->id,
        'item_key' => 'flood_zone',
    ]);

    $component = Livewire::test(PropertyDetailPage::class, ['property' => $property]);
    $instance = $component->instance();

    expect($instance->weightedScore)->toHaveKeys(['score', 'max', 'percentage'])
        ->and($instance->weightedScore['score'])->toBeGreaterThan(0)
        ->and($instance->checklistGroups)->toHaveCount(2)
        ->and($instance->checklistGroups)->toHaveKeys(['flood_environmental', 'energy_condition'])
        ->and($instance->checklistProgress)->toHaveKeys(['total', 'assessed', 'deal_breaker_total']);
});

test('unsaved property has empty checklist data', function () {
    $property = Property::factory()->create();

    $component = Livewire::test(PropertyDetailPage::class, ['property' => $property]);
    $instance = $component->instance();

    expect($instance->weightedScore)->toBe(['score' => 0, 'max' => 0, 'percentage' => 0])
        ->and($instance->checklistGroups)->toBe([])
        ->and($instance->checklistProgress)->toBe([]);
});

test('assessItem stores assessment and reloads data', function () {
    $property = Property::factory()->create();
    $saved = SavedProperty::factory()->create([
        'user_id' => $this->user->id,
        'property_id' => $property->id,
    ]);

    ChecklistTemplate::factory()->create([
        'key' => 'test_item',
        'label' => 'Test Item',
        'sort_order' => 1,
    ]);

    PropertyAssessment::create([
        'saved_property_id' => $saved->id,
        'item_key' => 'test_item',
        'assessment' => null,
        'is_auto_assessed' => false,
    ]);

    Livewire::test(PropertyDetailPage::class, ['property' => $property])
        ->call('assessItem', 'test_item', 'like');

    expect(PropertyAssessment::where('item_key', 'test_item')
        ->where('saved_property_id', $saved->id)
        ->first()->assessment)->toBe('like');
});

test('removeAssessment clears assessment', function () {
    $property = Property::factory()->create();
    $saved = SavedProperty::factory()->create([
        'user_id' => $this->user->id,
        'property_id' => $property->id,
    ]);

    ChecklistTemplate::factory()->create([
        'key' => 'test_item',
        'label' => 'Test Item',
        'sort_order' => 1,
    ]);

    PropertyAssessment::create([
        'saved_property_id' => $saved->id,
        'item_key' => 'test_item',
        'assessment' => 'like',
        'is_auto_assessed' => false,
    ]);

    Livewire::test(PropertyDetailPage::class, ['property' => $property])
        ->call('removeAssessment', 'test_item');

    expect(PropertyAssessment::where('item_key', 'test_item')
        ->where('saved_property_id', $saved->id)
        ->first()->assessment)->toBeNull();
});

test('addItemNote saves a note on the assessment', function () {
    $property = Property::factory()->create();
    $saved = SavedProperty::factory()->create([
        'user_id' => $this->user->id,
        'property_id' => $property->id,
    ]);

    ChecklistTemplate::factory()->create([
        'key' => 'test_item',
        'label' => 'Test Item',
        'sort_order' => 1,
    ]);

    PropertyAssessment::create([
        'saved_property_id' => $saved->id,
        'item_key' => 'test_item',
        'assessment' => null,
        'is_auto_assessed' => false,
    ]);

    Livewire::test(PropertyDetailPage::class, ['property' => $property])
        ->call('addItemNote', 'test_item', 'This is my note');

    expect(PropertyAssessment::where('item_key', 'test_item')
        ->where('saved_property_id', $saved->id)
        ->first()->notes)->toBe('This is my note');
});

test('addItemNote without saved property does nothing', function () {
    $property = Property::factory()->create();

    Livewire::test(PropertyDetailPage::class, ['property' => $property])
        ->call('addItemNote', 'test_item', 'Note text');

    expect(PropertyAssessment::count())->toBe(0);
});

test('getDealBreakerItems returns disliked deal-breakers from templates', function () {
    $property = Property::factory()->create();
    $saved = SavedProperty::factory()->create([
        'user_id' => $this->user->id,
        'property_id' => $property->id,
    ]);

    $template = ChecklistTemplate::factory()->dealBreaker()->create([
        'key' => 'flood_zone',
        'label' => 'Flood Zone',
        'sort_order' => 1,
    ]);

    ChecklistTemplate::factory()->important()->create([
        'key' => 'epc_rating',
        'label' => 'EPC Rating',
        'sort_order' => 2,
    ]);

    PropertyAssessment::create([
        'saved_property_id' => $saved->id,
        'item_key' => 'flood_zone',
        'assessment' => 'dislike',
        'is_auto_assessed' => false,
    ]);

    PropertyAssessment::create([
        'saved_property_id' => $saved->id,
        'item_key' => 'epc_rating',
        'assessment' => 'dislike',
        'is_auto_assessed' => false,
    ]);

    $component = Livewire::test(PropertyDetailPage::class, ['property' => $property]);
    $items = $component->instance()->getDealBreakerItems();

    expect($items)->toHaveCount(1)
        ->and($items[0]['key'])->toBe('flood_zone')
        ->and($items[0]['label'])->toBe('Flood Zone');
});

test('sidebar displays weighted score section for saved property', function () {
    $property = Property::factory()->create();
    SavedProperty::factory()->create([
        'user_id' => $this->user->id,
        'property_id' => $property->id,
    ]);

    ChecklistTemplate::factory()->create([
        'key' => 'test_item',
        'label' => 'Test Item',
        'sort_order' => 1,
    ]);

    Livewire::test(PropertyDetailPage::class, ['property' => $property])
        ->assertSee('Property Score')
        ->assertSee('Weighted Score');
});

test('sidebar displays category sections grouped by category', function () {
    $property = Property::factory()->create();
    SavedProperty::factory()->create([
        'user_id' => $this->user->id,
        'property_id' => $property->id,
    ]);

    ChecklistTemplate::factory()->create([
        'category' => 'flood_environmental',
        'category_label' => 'Flood & Environmental',
        'key' => 'flood_zone',
        'label' => 'Flood Zone',
        'sort_order' => 1,
    ]);

    ChecklistTemplate::factory()->create([
        'category' => 'energy_condition',
        'category_label' => 'Energy & Condition',
        'key' => 'epc_rating',
        'label' => 'EPC Rating',
        'sort_order' => 2,
    ]);

    Livewire::test(PropertyDetailPage::class, ['property' => $property])
        ->assertSee('Flood & Environmental')
        ->assertSee('Flood Zone')
        ->assertSee('Energy & Condition')
        ->assertSee('EPC Rating');
});

test('sidebar displays guidance text for manual items', function () {
    $property = Property::factory()->create();
    SavedProperty::factory()->create([
        'user_id' => $this->user->id,
        'property_id' => $property->id,
    ]);

    ChecklistTemplate::factory()->withGuidance()->create([
        'key' => 'manual_item',
        'label' => 'Manual Check',
        'guidance' => 'Check this during your visit',
        'sort_order' => 1,
    ]);

    Livewire::test(PropertyDetailPage::class, ['property' => $property])
        ->assertSee('Check this during your visit');
});

test('sidebar shows property notes section', function () {
    $property = Property::factory()->create();
    SavedProperty::factory()->create([
        'user_id' => $this->user->id,
        'property_id' => $property->id,
    ]);

    Livewire::test(PropertyDetailPage::class, ['property' => $property])
        ->assertSee('Property Notes')
        ->assertSee('Save Notes');
});
