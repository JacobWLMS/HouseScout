<?php

use App\Models\ChecklistTemplate;

test('checklist template can be created with factory', function () {
    $template = ChecklistTemplate::factory()->create();

    expect($template)->toBeInstanceOf(ChecklistTemplate::class)
        ->and($template->id)->toBeInt();
});

test('checklist template has correct fillable fields', function () {
    $template = ChecklistTemplate::factory()->create([
        'category' => 'flood_environmental',
        'category_label' => 'Flood & Environmental',
        'key' => 'flood_zone',
        'label' => 'Flood Zone',
        'severity' => 'deal_breaker',
        'type' => 'automated',
        'guidance' => 'Some guidance text',
        'link' => 'https://example.com',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    expect($template->category)->toBe('flood_environmental')
        ->and($template->category_label)->toBe('Flood & Environmental')
        ->and($template->key)->toBe('flood_zone')
        ->and($template->label)->toBe('Flood Zone')
        ->and($template->severity)->toBe('deal_breaker')
        ->and($template->type)->toBe('automated')
        ->and($template->guidance)->toBe('Some guidance text')
        ->and($template->link)->toBe('https://example.com')
        ->and($template->sort_order)->toBe(1)
        ->and($template->is_active)->toBeTrue();
});

test('checklist template casts is_active to boolean', function () {
    $template = ChecklistTemplate::factory()->create(['is_active' => 1]);

    expect($template->is_active)->toBeTrue()->toBeBool();
});

test('checklist template casts sort_order to integer', function () {
    $template = ChecklistTemplate::factory()->create(['sort_order' => '5']);

    expect($template->sort_order)->toBe(5)->toBeInt();
});

test('active scope returns only active templates', function () {
    ChecklistTemplate::factory()->create(['is_active' => true, 'key' => 'active-one']);
    ChecklistTemplate::factory()->create(['is_active' => false, 'key' => 'inactive-one']);

    $active = ChecklistTemplate::active()->get();

    expect($active)->toHaveCount(1)
        ->and($active->first()->key)->toBe('active-one');
});

test('byCategory scope filters by category', function () {
    ChecklistTemplate::factory()->create(['category' => 'flood_environmental', 'key' => 'flood-test']);
    ChecklistTemplate::factory()->create(['category' => 'crime', 'key' => 'crime-test']);

    $flood = ChecklistTemplate::byCategory('flood_environmental')->get();

    expect($flood)->toHaveCount(1)
        ->and($flood->first()->key)->toBe('flood-test');
});

test('dealBreakers scope returns only deal breaker items', function () {
    ChecklistTemplate::factory()->dealBreaker()->create(['key' => 'breaker-one']);
    ChecklistTemplate::factory()->important()->create(['key' => 'important-one']);
    ChecklistTemplate::factory()->niceToHave()->create(['key' => 'nice-one']);

    $dealBreakers = ChecklistTemplate::dealBreakers()->get();

    expect($dealBreakers)->toHaveCount(1)
        ->and($dealBreakers->first()->key)->toBe('breaker-one');
});

test('severity constants are defined correctly', function () {
    expect(ChecklistTemplate::DEAL_BREAKER)->toBe('deal_breaker')
        ->and(ChecklistTemplate::IMPORTANT)->toBe('important')
        ->and(ChecklistTemplate::NICE_TO_HAVE)->toBe('nice_to_have');
});

test('type constants are defined correctly', function () {
    expect(ChecklistTemplate::AUTOMATED)->toBe('automated')
        ->and(ChecklistTemplate::MANUAL)->toBe('manual');
});

test('factory deal breaker state sets severity', function () {
    $template = ChecklistTemplate::factory()->dealBreaker()->create();

    expect($template->severity)->toBe('deal_breaker');
});

test('factory important state sets severity', function () {
    $template = ChecklistTemplate::factory()->important()->create();

    expect($template->severity)->toBe('important');
});

test('factory nice to have state sets severity', function () {
    $template = ChecklistTemplate::factory()->niceToHave()->create();

    expect($template->severity)->toBe('nice_to_have');
});

test('factory automated state sets type', function () {
    $template = ChecklistTemplate::factory()->automated()->create();

    expect($template->type)->toBe('automated');
});

test('factory manual state sets type', function () {
    $template = ChecklistTemplate::factory()->manual()->create();

    expect($template->type)->toBe('manual');
});

test('factory with guidance state sets guidance', function () {
    $template = ChecklistTemplate::factory()->withGuidance()->create();

    expect($template->guidance)->not->toBeNull()->toBeString();
});

test('factory with link state sets link', function () {
    $template = ChecklistTemplate::factory()->withLink()->create();

    expect($template->link)->not->toBeNull()->toBeString();
});

test('key must be unique', function () {
    ChecklistTemplate::factory()->create(['key' => 'unique-key']);

    expect(fn () => ChecklistTemplate::factory()->create(['key' => 'unique-key']))
        ->toThrow(\Illuminate\Database\UniqueConstraintViolationException::class);
});

test('scopes can be chained', function () {
    ChecklistTemplate::factory()->dealBreaker()->create(['category' => 'flood_environmental', 'is_active' => true, 'key' => 'chain-test']);
    ChecklistTemplate::factory()->important()->create(['category' => 'flood_environmental', 'is_active' => true, 'key' => 'chain-test-2']);
    ChecklistTemplate::factory()->dealBreaker()->create(['category' => 'crime', 'is_active' => false, 'key' => 'chain-test-3']);

    $results = ChecklistTemplate::active()->byCategory('flood_environmental')->dealBreakers()->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->key)->toBe('chain-test');
});
