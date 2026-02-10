<?php

use App\Filament\Widgets\PropertySearchWidget;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    Queue::fake();
});

test('widget initial state has correct defaults', function () {
    Livewire::test(PropertySearchWidget::class)
        ->assertSet('query', '')
        ->assertSet('postcodeSuggestions', [])
        ->assertSet('selectedPostcode', null)
        ->assertSet('addresses', [])
        ->assertSet('latitude', null)
        ->assertSet('longitude', null)
        ->assertSet('adminDistrict', null)
        ->assertSet('adminCounty', null)
        ->assertSet('isLoadingAddresses', false)
        ->assertSet('showDropdown', false);
});

test('autocomplete api failure returns empty suggestions gracefully', function () {
    Http::fake([
        'api.postcodes.io/*' => Http::response(null, 500),
    ]);

    Livewire::test(PropertySearchWidget::class)
        ->set('query', 'SW1A')
        ->assertSet('postcodeSuggestions', [])
        ->assertSet('showDropdown', false);
});

test('postcode lookup api failure during selectPostcode handles gracefully', function () {
    config(['housescout.api.epc.key' => 'test-key', 'housescout.api.epc.email' => 'test@example.com']);

    Http::fake([
        'api.postcodes.io/*' => Http::response(null, 500),
        'epc.opendatacommunities.org/*' => Http::response(['rows' => []]),
    ]);

    Livewire::test(PropertySearchWidget::class)
        ->call('selectPostcode', 'SW1A 1AA')
        ->assertSet('latitude', null)
        ->assertSet('longitude', null)
        ->assertSet('adminDistrict', null)
        ->assertSet('isLoadingAddresses', false);
});

test('empty query after having suggestions clears them', function () {
    Http::fake([
        'api.postcodes.io/*' => Http::response([
            'status' => 200,
            'result' => ['SW1A 0AA', 'SW1A 1AA'],
        ]),
    ]);

    Livewire::test(PropertySearchWidget::class)
        ->set('query', 'SW1A')
        ->assertSet('showDropdown', true)
        ->assertCount('postcodeSuggestions', 2)
        ->set('query', '')
        ->assertSet('postcodeSuggestions', [])
        ->assertSet('showDropdown', false);
});

test('closeDropdown method sets showDropdown to false', function () {
    Livewire::test(PropertySearchWidget::class)
        ->set('showDropdown', true)
        ->call('closeDropdown')
        ->assertSet('showDropdown', false);
});

test('selectAddress without latitude and longitude shows error notification', function () {
    $addresses = [
        [
            'full_address' => '10, DOWNING STREET',
            'address_line_1' => '10 Downing Street',
            'address_line_2' => null,
            'postcode' => 'SW1A 1AA',
            'uprn' => '100023336956',
            'property_type' => 'House',
            'energy_rating' => 'D',
            'floor_area' => 100.0,
        ],
    ];

    Livewire::test(PropertySearchWidget::class)
        ->set('addresses', $addresses)
        ->set('latitude', null)
        ->set('longitude', null)
        ->call('selectAddress', 0)
        ->assertNoRedirect()
        ->assertNotified('Search Error');
});

test('typing new query after selecting postcode resets address state', function () {
    Http::fake([
        'api.postcodes.io/*' => Http::response([
            'status' => 200,
            'result' => ['EC1A 1AA'],
        ]),
    ]);

    Livewire::test(PropertySearchWidget::class)
        ->set('selectedPostcode', 'SW1A 1AA')
        ->set('latitude', 51.5)
        ->set('longitude', -0.14)
        ->set('adminDistrict', 'Westminster')
        ->set('addresses', [[
            'full_address' => '10 Test Street',
            'address_line_1' => '10 Test Street',
            'address_line_2' => null,
            'postcode' => 'SW1A 1AA',
            'uprn' => '123456789',
            'property_type' => 'House',
            'energy_rating' => 'D',
            'floor_area' => 100.0,
        ]])
        ->set('query', 'EC1A')
        ->assertSet('selectedPostcode', null)
        ->assertSet('addresses', [])
        ->assertSet('latitude', null)
        ->assertSet('longitude', null)
        ->assertSet('adminDistrict', null);
});

test('special characters in query do not crash widget', function () {
    Http::fake([
        'api.postcodes.io/*' => Http::response([
            'status' => 200,
            'result' => null,
        ]),
    ]);

    Livewire::test(PropertySearchWidget::class)
        ->set('query', '<script>alert("xss")</script>')
        ->assertSet('postcodeSuggestions', [])
        ->assertHasNoErrors();
});

test('very long query string is handled without error', function () {
    Http::fake([
        'api.postcodes.io/*' => Http::response([
            'status' => 200,
            'result' => null,
        ]),
    ]);

    $longQuery = str_repeat('A', 500);

    Livewire::test(PropertySearchWidget::class)
        ->set('query', $longQuery)
        ->assertSet('postcodeSuggestions', [])
        ->assertHasNoErrors();
});

test('missing epc api key during selectPostcode shows configuration notification', function () {
    config(['housescout.api.epc.key' => null]);

    Http::fake([
        'api.postcodes.io/*' => Http::response([
            'status' => 200,
            'result' => [
                'postcode' => 'SW1A 1AA',
                'latitude' => 51.501009,
                'longitude' => -0.141588,
                'admin_district' => 'Westminster',
                'admin_county' => null,
            ],
        ]),
    ]);

    Livewire::test(PropertySearchWidget::class)
        ->call('selectPostcode', 'SW1A 1AA')
        ->assertSet('isLoadingAddresses', false)
        ->assertSet('showDropdown', false)
        ->assertNotified('Configuration Required');
});
