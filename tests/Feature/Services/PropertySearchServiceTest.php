<?php

use App\DataObjects\AddressResult;
use App\DataObjects\PostcodeLookupResult;
use App\Exceptions\InvalidPostcodeException;
use App\Jobs\FetchPropertyDataJob;
use App\Models\Property;
use App\Models\PropertySearch;
use App\Models\User;
use App\Services\PostcodeService;
use App\Services\PropertySearchService;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    Queue::fake();
    $this->service = new PropertySearchService(new PostcodeService);
});

// --- selectProperty tests ---

test('selectProperty creates new property from address result', function () {
    $user = User::factory()->create();

    $address = new AddressResult(
        fullAddress: '10, DOWNING STREET',
        addressLine1: '10 Downing Street',
        addressLine2: null,
        postcode: 'SW1A 2AA',
        uprn: '100023336956',
        propertyType: 'House',
        energyRating: 'D',
        floorArea: 100.0,
    );

    $postcodeData = new PostcodeLookupResult(
        postcode: 'SW1A 2AA',
        latitude: 51.5034,
        longitude: -0.1276,
        adminDistrict: 'Westminster',
        adminCounty: null,
        lsoa: 'Westminster 018C',
        msoa: 'Westminster 018',
        ward: "St James's",
        constituency: 'Cities of London and Westminster',
        easting: 530268,
        northing: 179951,
        localAuthority: 'Westminster',
    );

    $property = $this->service->selectProperty($user, $address, $postcodeData);

    expect($property)->toBeInstanceOf(Property::class)
        ->and($property->address_line_1)->toBe('10 Downing Street')
        ->and($property->postcode)->toBe('SW1A 2AA')
        ->and($property->uprn)->toBe('100023336956')
        ->and($property->latitude)->not->toBeNull()
        ->and($property->longitude)->not->toBeNull()
        ->and($property->city)->toBe('Westminster')
        ->and($property->property_type)->toBe('House')
        ->and($property->lsoa)->toBe('Westminster 018C')
        ->and($property->msoa)->toBe('Westminster 018')
        ->and($property->ward)->toBe("St James's")
        ->and($property->constituency)->toBe('Cities of London and Westminster')
        ->and($property->easting)->toBe(530268)
        ->and($property->northing)->toBe(179951)
        ->and($property->local_authority)->toBe('Westminster');
});

test('selectProperty finds existing property by UPRN', function () {
    $user = User::factory()->create();
    $existing = Property::factory()->create(['uprn' => '100023336956']);

    $address = new AddressResult(
        fullAddress: '10, DOWNING STREET',
        addressLine1: '10 Downing Street',
        addressLine2: null,
        postcode: 'SW1A 2AA',
        uprn: '100023336956',
        propertyType: 'House',
        energyRating: 'D',
        floorArea: 100.0,
    );

    $postcodeData = new PostcodeLookupResult(
        postcode: 'SW1A 2AA',
        latitude: 51.5034,
        longitude: -0.1276,
        adminDistrict: 'Westminster',
        adminCounty: null,
    );

    $property = $this->service->selectProperty($user, $address, $postcodeData);

    expect($property->id)->toBe($existing->id);
    expect(Property::count())->toBe(1);
});

test('selectProperty finds existing property by address and postcode', function () {
    $user = User::factory()->create();
    $existing = Property::factory()->create([
        'address_line_1' => '10 Downing Street',
        'postcode' => 'SW1A 2AA',
        'uprn' => null,
    ]);

    $address = new AddressResult(
        fullAddress: '10, DOWNING STREET',
        addressLine1: '10 Downing Street',
        addressLine2: null,
        postcode: 'SW1A 2AA',
        uprn: '999999999',
        propertyType: 'House',
        energyRating: 'D',
        floorArea: 100.0,
    );

    $postcodeData = new PostcodeLookupResult(
        postcode: 'SW1A 2AA',
        latitude: 51.5034,
        longitude: -0.1276,
        adminDistrict: 'Westminster',
        adminCounty: null,
    );

    $property = $this->service->selectProperty($user, $address, $postcodeData);

    expect($property->id)->toBe($existing->id);
    expect(Property::count())->toBe(1);
});

test('selectProperty creates audit record', function () {
    $user = User::factory()->create();

    $address = new AddressResult(
        fullAddress: '10, DOWNING STREET',
        addressLine1: '10 Downing Street',
        addressLine2: null,
        postcode: 'SW1A 2AA',
        uprn: '100023336956',
        propertyType: 'House',
        energyRating: 'D',
        floorArea: 100.0,
    );

    $postcodeData = new PostcodeLookupResult(
        postcode: 'SW1A 2AA',
        latitude: 51.5034,
        longitude: -0.1276,
        adminDistrict: 'Westminster',
        adminCounty: null,
    );

    $this->service->selectProperty($user, $address, $postcodeData);

    expect(PropertySearch::query()->where('user_id', $user->id)->count())->toBe(1);
});

test('selectProperty dispatches FetchPropertyDataJob', function () {
    $user = User::factory()->create();

    $address = new AddressResult(
        fullAddress: '10, DOWNING STREET',
        addressLine1: '10 Downing Street',
        addressLine2: null,
        postcode: 'SW1A 2AA',
        uprn: '100023336956',
        propertyType: 'House',
        energyRating: 'D',
        floorArea: 100.0,
    );

    $postcodeData = new PostcodeLookupResult(
        postcode: 'SW1A 2AA',
        latitude: 51.5034,
        longitude: -0.1276,
        adminDistrict: 'Westminster',
        adminCounty: null,
    );

    $this->service->selectProperty($user, $address, $postcodeData);

    Queue::assertPushed(FetchPropertyDataJob::class);
});

// --- search (text fallback) tests ---

test('search finds existing property by address text', function () {
    $user = User::factory()->create();
    $existing = Property::factory()->create(['address_line_1' => '10 Downing Street']);

    $property = $this->service->search($user, 'Downing');

    expect($property->id)->toBe($existing->id);
});

test('search throws InvalidPostcodeException when no property found', function () {
    $user = User::factory()->create();

    expect(fn () => $this->service->search($user, 'non-existent address'))
        ->toThrow(InvalidPostcodeException::class);
});

test('search logs the search in property_searches', function () {
    $user = User::factory()->create();
    Property::factory()->create(['postcode' => 'SW1A 1AA']);

    $this->service->search($user, 'SW1A 1AA');

    expect(PropertySearch::query()->where('user_id', $user->id)->count())->toBe(1);
});

test('search dispatches FetchPropertyDataJob', function () {
    $user = User::factory()->create();
    Property::factory()->create(['postcode' => 'EC1A 1BB']);

    $this->service->search($user, 'EC1A 1BB');

    Queue::assertPushed(FetchPropertyDataJob::class);
});

// --- getRecentSearches and getDemandCount tests ---

test('get recent searches returns latest searches for user', function () {
    $user = User::factory()->create();
    $property = Property::factory()->create();

    PropertySearch::factory()->count(3)->create([
        'user_id' => $user->id,
        'property_id' => $property->id,
    ]);

    $searches = $this->service->getRecentSearches($user);

    expect($searches)->toHaveCount(3);
});

test('get recent searches respects limit', function () {
    $user = User::factory()->create();
    $property = Property::factory()->create();

    PropertySearch::factory()->count(5)->create([
        'user_id' => $user->id,
        'property_id' => $property->id,
    ]);

    $searches = $this->service->getRecentSearches($user, 2);

    expect($searches)->toHaveCount(2);
});

test('get demand count returns unique user count within days', function () {
    $property = Property::factory()->create();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    PropertySearch::factory()->create([
        'user_id' => $user1->id,
        'property_id' => $property->id,
        'searched_at' => now()->subDays(5),
    ]);

    PropertySearch::factory()->create([
        'user_id' => $user2->id,
        'property_id' => $property->id,
        'searched_at' => now()->subDays(10),
    ]);

    PropertySearch::factory()->create([
        'user_id' => $user1->id,
        'property_id' => $property->id,
        'searched_at' => now()->subDays(2),
    ]);

    $count = $this->service->getDemandCount($property);

    expect($count)->toBe(2);
});

test('get demand count excludes searches older than specified days', function () {
    $property = Property::factory()->create();
    $user = User::factory()->create();

    PropertySearch::factory()->create([
        'user_id' => $user->id,
        'property_id' => $property->id,
        'searched_at' => now()->subDays(60),
    ]);

    $count = $this->service->getDemandCount($property, 30);

    expect($count)->toBe(0);
});
