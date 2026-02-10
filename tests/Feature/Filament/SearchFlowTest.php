<?php

use App\Filament\Widgets\PropertySearchWidget;
use App\Jobs\FetchPropertyDataJob;
use App\Models\Property;
use App\Models\PropertySearch;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    Queue::fake();
});

test('search widget renders on dashboard', function () {
    $response = $this->get('/app');

    $response->assertStatus(200);
    $response->assertSeeLivewire(PropertySearchWidget::class);
});

test('typing in search triggers postcode autocomplete', function () {
    Http::fake([
        'api.postcodes.io/postcodes/SW1A/autocomplete' => Http::response([
            'status' => 200,
            'result' => ['SW1A 0AA', 'SW1A 0PW', 'SW1A 1AA'],
        ]),
    ]);

    Livewire::test(PropertySearchWidget::class)
        ->set('query', 'SW1A')
        ->assertSet('postcodeSuggestions', ['SW1A 0AA', 'SW1A 0PW', 'SW1A 1AA'])
        ->assertSet('showDropdown', true);
});

test('short query does not trigger autocomplete', function () {
    Livewire::test(PropertySearchWidget::class)
        ->set('query', 'S')
        ->assertSet('postcodeSuggestions', [])
        ->assertSet('showDropdown', false);
});

test('selecting a postcode loads addresses', function () {
    config(['housescout.api.epc.key' => 'test-key', 'housescout.api.epc.email' => 'test@example.com']);

    Http::fake([
        'api.postcodes.io/postcodes/SW1A 1AA' => Http::response([
            'status' => 200,
            'result' => [
                'postcode' => 'SW1A 1AA',
                'latitude' => 51.501009,
                'longitude' => -0.141588,
                'admin_district' => 'Westminster',
                'admin_county' => null,
            ],
        ]),
        'epc.opendatacommunities.org/*' => Http::response([
            'rows' => [
                [
                    'address' => '10, DOWNING STREET',
                    'uprn' => '100023336956',
                    'property-type' => 'House',
                    'current-energy-rating' => 'D',
                    'total-floor-area' => '100',
                    'lodgement-date' => '2020-06-15',
                ],
                [
                    'address' => '11, DOWNING STREET',
                    'uprn' => '100023336957',
                    'property-type' => 'House',
                    'current-energy-rating' => 'C',
                    'total-floor-area' => '120',
                    'lodgement-date' => '2021-03-10',
                ],
            ],
        ]),
    ]);

    Livewire::test(PropertySearchWidget::class)
        ->call('selectPostcode', 'SW1A 1AA')
        ->assertSet('selectedPostcode', 'SW1A 1AA')
        ->assertSet('query', 'SW1A 1AA')
        ->assertSet('adminDistrict', 'Westminster')
        ->assertSet('isLoadingAddresses', false)
        ->assertSet('showDropdown', true)
        ->assertCount('addresses', 2);
});

test('selecting an address creates property and redirects', function () {
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
        ->set('selectedPostcode', 'SW1A 1AA')
        ->set('latitude', 51.501009)
        ->set('longitude', -0.141588)
        ->set('adminDistrict', 'Westminster')
        ->set('addresses', $addresses)
        ->call('selectAddress', 0)
        ->assertRedirect();

    $property = Property::where('uprn', '100023336956')->first();

    expect($property)->not->toBeNull()
        ->and($property->address_line_1)->toBe('10 Downing Street')
        ->and($property->postcode)->toBe('SW1A 1AA')
        ->and($property->uprn)->toBe('100023336956')
        ->and($property->latitude)->not->toBeNull()
        ->and($property->longitude)->not->toBeNull()
        ->and($property->city)->toBe('Westminster');

    expect(PropertySearch::where('user_id', $this->user->id)->count())->toBe(1);

    Queue::assertPushed(FetchPropertyDataJob::class);
});

test('selecting address with invalid index does nothing', function () {
    Livewire::test(PropertySearchWidget::class)
        ->call('selectAddress', 99)
        ->assertNoRedirect();

    expect(Property::count())->toBe(0);
});

test('reset search clears all state', function () {
    Http::fake([
        'api.postcodes.io/*' => Http::response([
            'status' => 200,
            'result' => ['SW1A 0AA', 'SW1A 1AA'],
        ]),
    ]);

    Livewire::test(PropertySearchWidget::class)
        ->set('query', 'SW1A')
        ->call('resetSearch')
        ->assertSet('query', '')
        ->assertSet('postcodeSuggestions', [])
        ->assertSet('selectedPostcode', null)
        ->assertSet('addresses', [])
        ->assertSet('showDropdown', false);
});

test('selecting postcode with no addresses shows notification', function () {
    config(['housescout.api.epc.key' => 'test-key', 'housescout.api.epc.email' => 'test@example.com']);

    Http::fake([
        'api.postcodes.io/postcodes/ZZ99 9ZZ' => Http::response([
            'status' => 200,
            'result' => [
                'postcode' => 'ZZ99 9ZZ',
                'latitude' => 51.0,
                'longitude' => -0.1,
                'admin_district' => 'Test',
                'admin_county' => null,
            ],
        ]),
        'epc.opendatacommunities.org/*' => Http::response([
            'rows' => [],
        ]),
    ]);

    Livewire::test(PropertySearchWidget::class)
        ->call('selectPostcode', 'ZZ99 9ZZ')
        ->assertSet('addresses', [])
        ->assertSet('showDropdown', false)
        ->assertNotified('No addresses found');
});

test('selecting postcode with missing EPC API key shows configuration error', function () {
    config(['housescout.api.epc.key' => null]);

    Http::fake([
        'api.postcodes.io/postcodes/SW1A 1AA' => Http::response([
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
        ->assertSet('addresses', [])
        ->assertSet('isLoadingAddresses', false)
        ->assertSet('showDropdown', false)
        ->assertNotified('Configuration Required');
});

test('selecting same address twice does not create duplicate property', function () {
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

    // First selection
    Livewire::test(PropertySearchWidget::class)
        ->set('selectedPostcode', 'SW1A 1AA')
        ->set('latitude', 51.501009)
        ->set('longitude', -0.141588)
        ->set('adminDistrict', 'Westminster')
        ->set('addresses', $addresses)
        ->call('selectAddress', 0);

    // Second selection
    Livewire::test(PropertySearchWidget::class)
        ->set('selectedPostcode', 'SW1A 1AA')
        ->set('latitude', 51.501009)
        ->set('longitude', -0.141588)
        ->set('adminDistrict', 'Westminster')
        ->set('addresses', $addresses)
        ->call('selectAddress', 0);

    expect(Property::where('uprn', '100023336956')->count())->toBe(1);
    expect(PropertySearch::where('user_id', $this->user->id)->count())->toBe(2);
});
