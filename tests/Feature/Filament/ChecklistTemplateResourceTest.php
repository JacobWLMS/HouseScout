<?php

use App\Filament\Resources\ChecklistTemplates\Pages\CreateChecklistTemplate;
use App\Filament\Resources\ChecklistTemplates\Pages\EditChecklistTemplate;
use App\Filament\Resources\ChecklistTemplates\Pages\ListChecklistTemplates;
use App\Models\ChecklistTemplate;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('list page renders successfully', function () {
    $response = $this->get('/app/checklist-templates');

    $response->assertStatus(200);
});

test('list page requires authentication', function () {
    auth()->logout();

    $response = $this->get('/app/checklist-templates');

    $response->assertRedirect('/app/login');
});

test('list page displays checklist templates', function () {
    ChecklistTemplate::factory()->create([
        'label' => 'Flood Zone',
        'category_label' => 'Flood & Environmental',
    ]);

    Livewire::test(ListChecklistTemplates::class)
        ->assertSee('Flood Zone')
        ->assertSee('Flood & Environmental');
});

test('list page defaults to sort_order ascending', function () {
    ChecklistTemplate::factory()->create([
        'label' => 'Second Item',
        'sort_order' => 20,
    ]);

    ChecklistTemplate::factory()->create([
        'label' => 'First Item',
        'sort_order' => 10,
    ]);

    Livewire::test(ListChecklistTemplates::class)
        ->assertSeeInOrder(['First Item', 'Second Item']);
});

test('list page shows severity badges', function () {
    ChecklistTemplate::factory()->dealBreaker()->create([
        'label' => 'Critical Item',
    ]);

    ChecklistTemplate::factory()->niceToHave()->create([
        'label' => 'Optional Item',
    ]);

    Livewire::test(ListChecklistTemplates::class)
        ->assertSee('Critical Item')
        ->assertSee('Optional Item');
});

test('create page renders successfully', function () {
    $response = $this->get('/app/checklist-templates/create');

    $response->assertStatus(200);
});

test('can create a checklist template', function () {
    Livewire::test(CreateChecklistTemplate::class)
        ->fillForm([
            'category' => 'flood_environmental',
            'category_label' => 'Flood & Environmental',
            'key' => 'test_item',
            'label' => 'Test Item',
            'severity' => 'deal_breaker',
            'type' => 'manual',
            'sort_order' => 5,
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('checklist_templates', [
        'key' => 'test_item',
        'label' => 'Test Item',
        'severity' => 'deal_breaker',
        'type' => 'manual',
        'sort_order' => 5,
        'is_active' => true,
    ]);
});

test('can create a template with guidance and link', function () {
    Livewire::test(CreateChecklistTemplate::class)
        ->fillForm([
            'category' => 'legal_title',
            'category_label' => 'Legal & Title',
            'key' => 'guidance_item',
            'label' => 'Guidance Item',
            'severity' => 'important',
            'type' => 'manual',
            'guidance' => 'Check with your solicitor',
            'link' => 'https://example.com',
            'sort_order' => 10,
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('checklist_templates', [
        'key' => 'guidance_item',
        'guidance' => 'Check with your solicitor',
        'link' => 'https://example.com',
    ]);
});

test('edit page renders successfully', function () {
    $template = ChecklistTemplate::factory()->create();

    $response = $this->get("/app/checklist-templates/{$template->id}/edit");

    $response->assertStatus(200);
});

test('can edit a checklist template', function () {
    $template = ChecklistTemplate::factory()->create([
        'key' => 'original_key',
        'label' => 'Original Label',
    ]);

    Livewire::test(EditChecklistTemplate::class, ['record' => $template->id])
        ->fillForm([
            'label' => 'Updated Label',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('checklist_templates', [
        'id' => $template->id,
        'label' => 'Updated Label',
    ]);
});

test('validation requires category', function () {
    Livewire::test(CreateChecklistTemplate::class)
        ->fillForm([
            'category' => null,
            'category_label' => 'Test',
            'key' => 'test',
            'label' => 'Test',
            'severity' => 'important',
            'type' => 'manual',
            'sort_order' => 0,
        ])
        ->call('create')
        ->assertHasFormErrors(['category' => 'required']);
});

test('validation requires unique key', function () {
    ChecklistTemplate::factory()->create(['key' => 'existing_key']);

    Livewire::test(CreateChecklistTemplate::class)
        ->fillForm([
            'category' => 'crime',
            'category_label' => 'Crime',
            'key' => 'existing_key',
            'label' => 'Duplicate Key Item',
            'severity' => 'important',
            'type' => 'manual',
            'sort_order' => 0,
        ])
        ->call('create')
        ->assertHasFormErrors(['key' => 'unique']);
});

test('validation allows same key when editing same record', function () {
    $template = ChecklistTemplate::factory()->create(['key' => 'my_key']);

    Livewire::test(EditChecklistTemplate::class, ['record' => $template->id])
        ->fillForm([
            'key' => 'my_key',
            'label' => 'Updated Label',
        ])
        ->call('save')
        ->assertHasNoFormErrors();
});

test('validation requires key to be alpha dash', function () {
    Livewire::test(CreateChecklistTemplate::class)
        ->fillForm([
            'category' => 'crime',
            'category_label' => 'Crime',
            'key' => 'invalid key with spaces',
            'label' => 'Test',
            'severity' => 'important',
            'type' => 'manual',
            'sort_order' => 0,
        ])
        ->call('create')
        ->assertHasFormErrors(['key']);
});

test('can delete a checklist template', function () {
    $template = ChecklistTemplate::factory()->create();

    Livewire::test(EditChecklistTemplate::class, ['record' => $template->id])
        ->callAction('delete');

    $this->assertDatabaseMissing('checklist_templates', [
        'id' => $template->id,
    ]);
});

test('can filter by category', function () {
    ChecklistTemplate::factory()->create([
        'category' => 'flood_environmental',
        'label' => 'Flood Item',
    ]);

    ChecklistTemplate::factory()->create([
        'category' => 'crime',
        'label' => 'Crime Item',
    ]);

    Livewire::test(ListChecklistTemplates::class)
        ->filterTable('category', 'flood_environmental')
        ->assertSee('Flood Item')
        ->assertDontSee('Crime Item');
});

test('can filter by severity', function () {
    ChecklistTemplate::factory()->dealBreaker()->create([
        'label' => 'Deal Breaker Item',
    ]);

    ChecklistTemplate::factory()->niceToHave()->create([
        'label' => 'Nice Item',
    ]);

    Livewire::test(ListChecklistTemplates::class)
        ->filterTable('severity', 'deal_breaker')
        ->assertSee('Deal Breaker Item')
        ->assertDontSee('Nice Item');
});

test('can filter by type', function () {
    ChecklistTemplate::factory()->automated()->create([
        'label' => 'Auto Item',
    ]);

    ChecklistTemplate::factory()->manual()->create([
        'label' => 'Manual Item',
    ]);

    Livewire::test(ListChecklistTemplates::class)
        ->filterTable('type', 'automated')
        ->assertSee('Auto Item')
        ->assertDontSee('Manual Item');
});

test('empty state works when no templates exist', function () {
    Livewire::test(ListChecklistTemplates::class)
        ->assertSuccessful();
});

test('can search templates by label', function () {
    ChecklistTemplate::factory()->create(['label' => 'Flood Zone']);
    ChecklistTemplate::factory()->create(['label' => 'EPC Rating']);

    Livewire::test(ListChecklistTemplates::class)
        ->searchTable('Flood')
        ->assertSee('Flood Zone')
        ->assertDontSee('EPC Rating');
});
