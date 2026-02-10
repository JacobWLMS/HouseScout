<?php

use App\DataObjects\AddressResult;
use App\DataObjects\PostcodeLookupResult;

// --- PostcodeLookupResult ---

test('PostcodeLookupResult constructor creates valid object', function () {
    $result = new PostcodeLookupResult(
        postcode: 'SW1A 1AA',
        latitude: 51.5014,
        longitude: -0.1419,
        adminDistrict: 'Westminster',
        adminCounty: null,
    );

    expect($result->postcode)->toBe('SW1A 1AA')
        ->and($result->latitude)->toBe(51.5014)
        ->and($result->longitude)->toBe(-0.1419)
        ->and($result->adminDistrict)->toBe('Westminster')
        ->and($result->adminCounty)->toBeNull();
});

test('PostcodeLookupResult toArray returns correct keys and values', function () {
    $result = new PostcodeLookupResult(
        postcode: 'EC2R 8AH',
        latitude: 51.5155,
        longitude: -0.0922,
        adminDistrict: 'City of London',
        adminCounty: 'Greater London',
    );

    $array = $result->toArray();

    expect($array)->toHaveKeys(['postcode', 'latitude', 'longitude', 'admin_district', 'admin_county'])
        ->and($array['postcode'])->toBe('EC2R 8AH')
        ->and($array['latitude'])->toBe(51.5155)
        ->and($array['longitude'])->toBe(-0.0922)
        ->and($array['admin_district'])->toBe('City of London')
        ->and($array['admin_county'])->toBe('Greater London');
});

test('PostcodeLookupResult fromArray creates valid object', function () {
    $data = [
        'postcode' => 'M1 1AE',
        'latitude' => 53.4808,
        'longitude' => -2.2426,
        'admin_district' => 'Manchester',
        'admin_county' => 'Greater Manchester',
    ];

    $result = PostcodeLookupResult::fromArray($data);

    expect($result->postcode)->toBe('M1 1AE')
        ->and($result->latitude)->toBe(53.4808)
        ->and($result->longitude)->toBe(-2.2426)
        ->and($result->adminDistrict)->toBe('Manchester')
        ->and($result->adminCounty)->toBe('Greater Manchester');
});

test('PostcodeLookupResult fromArray handles missing optional fields', function () {
    $data = [
        'postcode' => 'SW1A 1AA',
        'latitude' => 51.5014,
        'longitude' => -0.1419,
    ];

    $result = PostcodeLookupResult::fromArray($data);

    expect($result->postcode)->toBe('SW1A 1AA')
        ->and($result->adminDistrict)->toBeNull()
        ->and($result->adminCounty)->toBeNull();
});

test('PostcodeLookupResult fromArray casts latitude and longitude to float', function () {
    $data = [
        'postcode' => 'SW1A 1AA',
        'latitude' => '51.5014',
        'longitude' => '-0.1419',
    ];

    $result = PostcodeLookupResult::fromArray($data);

    expect($result->latitude)->toBeFloat()->toBe(51.5014)
        ->and($result->longitude)->toBeFloat()->toBe(-0.1419);
});

test('PostcodeLookupResult roundtrips through toArray and fromArray', function () {
    $original = new PostcodeLookupResult(
        postcode: 'LS1 1UR',
        latitude: 53.7997,
        longitude: -1.5492,
        adminDistrict: 'Leeds',
        adminCounty: 'West Yorkshire',
    );

    $roundtripped = PostcodeLookupResult::fromArray($original->toArray());

    expect($roundtripped->postcode)->toBe($original->postcode)
        ->and($roundtripped->latitude)->toBe($original->latitude)
        ->and($roundtripped->longitude)->toBe($original->longitude)
        ->and($roundtripped->adminDistrict)->toBe($original->adminDistrict)
        ->and($roundtripped->adminCounty)->toBe($original->adminCounty);
});

test('PostcodeLookupResult roundtrips with null optional fields', function () {
    $original = new PostcodeLookupResult(
        postcode: 'EH1 1YZ',
        latitude: 55.9533,
        longitude: -3.1883,
        adminDistrict: null,
        adminCounty: null,
    );

    $array = $original->toArray();
    $roundtripped = PostcodeLookupResult::fromArray($array);

    expect($roundtripped->adminDistrict)->toBeNull()
        ->and($roundtripped->adminCounty)->toBeNull();
});

// --- AddressResult ---

test('AddressResult constructor creates valid object', function () {
    $result = new AddressResult(
        fullAddress: '10 Downing Street, London, SW1A 2AA',
        addressLine1: '10 Downing Street',
        addressLine2: null,
        postcode: 'SW1A 2AA',
        uprn: '100023336956',
        propertyType: 'Terraced',
        energyRating: 'C',
        floorArea: 150.5,
    );

    expect($result->fullAddress)->toBe('10 Downing Street, London, SW1A 2AA')
        ->and($result->addressLine1)->toBe('10 Downing Street')
        ->and($result->addressLine2)->toBeNull()
        ->and($result->postcode)->toBe('SW1A 2AA')
        ->and($result->uprn)->toBe('100023336956')
        ->and($result->propertyType)->toBe('Terraced')
        ->and($result->energyRating)->toBe('C')
        ->and($result->floorArea)->toBe(150.5);
});

test('AddressResult toArray returns correct keys and values', function () {
    $result = new AddressResult(
        fullAddress: '42 Elm Street, Bristol, BS1 1EH',
        addressLine1: '42 Elm Street',
        addressLine2: 'Flat A',
        postcode: 'BS1 1EH',
        uprn: '200012345678',
        propertyType: 'Flat',
        energyRating: 'B',
        floorArea: 65.25,
    );

    $array = $result->toArray();

    expect($array)->toHaveKeys([
        'full_address', 'address_line_1', 'address_line_2',
        'postcode', 'uprn', 'property_type', 'energy_rating', 'floor_area',
    ])
        ->and($array['full_address'])->toBe('42 Elm Street, Bristol, BS1 1EH')
        ->and($array['address_line_1'])->toBe('42 Elm Street')
        ->and($array['address_line_2'])->toBe('Flat A')
        ->and($array['postcode'])->toBe('BS1 1EH')
        ->and($array['uprn'])->toBe('200012345678')
        ->and($array['property_type'])->toBe('Flat')
        ->and($array['energy_rating'])->toBe('B')
        ->and($array['floor_area'])->toBe(65.25);
});

test('AddressResult fromArray creates valid object', function () {
    $data = [
        'full_address' => '5 High Street, Oxford, OX1 1BX',
        'address_line_1' => '5 High Street',
        'address_line_2' => 'Unit 3',
        'postcode' => 'OX1 1BX',
        'uprn' => '300098765432',
        'property_type' => 'Semi-Detached',
        'energy_rating' => 'D',
        'floor_area' => 89.0,
    ];

    $result = AddressResult::fromArray($data);

    expect($result->fullAddress)->toBe('5 High Street, Oxford, OX1 1BX')
        ->and($result->addressLine1)->toBe('5 High Street')
        ->and($result->addressLine2)->toBe('Unit 3')
        ->and($result->postcode)->toBe('OX1 1BX')
        ->and($result->uprn)->toBe('300098765432')
        ->and($result->propertyType)->toBe('Semi-Detached')
        ->and($result->energyRating)->toBe('D')
        ->and($result->floorArea)->toBe(89.0);
});

test('AddressResult fromArray handles missing optional fields', function () {
    $data = [
        'full_address' => '1 Main Road, London, SW1A 1AA',
        'address_line_1' => '1 Main Road',
        'postcode' => 'SW1A 1AA',
    ];

    $result = AddressResult::fromArray($data);

    expect($result->fullAddress)->toBe('1 Main Road, London, SW1A 1AA')
        ->and($result->addressLine1)->toBe('1 Main Road')
        ->and($result->addressLine2)->toBeNull()
        ->and($result->uprn)->toBeNull()
        ->and($result->propertyType)->toBeNull()
        ->and($result->energyRating)->toBeNull()
        ->and($result->floorArea)->toBeNull();
});

test('AddressResult fromArray casts floor_area to float', function () {
    $data = [
        'full_address' => '1 Main Road, London, SW1A 1AA',
        'address_line_1' => '1 Main Road',
        'postcode' => 'SW1A 1AA',
        'floor_area' => '125',
    ];

    $result = AddressResult::fromArray($data);

    expect($result->floorArea)->toBeFloat()->toBe(125.0);
});

test('AddressResult fromArray does not cast null floor_area', function () {
    $data = [
        'full_address' => '1 Main Road, London, SW1A 1AA',
        'address_line_1' => '1 Main Road',
        'postcode' => 'SW1A 1AA',
        'floor_area' => null,
    ];

    $result = AddressResult::fromArray($data);

    expect($result->floorArea)->toBeNull();
});

test('AddressResult roundtrips through toArray and fromArray', function () {
    $original = new AddressResult(
        fullAddress: '25 Park Lane, Manchester, M1 1AE',
        addressLine1: '25 Park Lane',
        addressLine2: 'Apartment 12',
        postcode: 'M1 1AE',
        uprn: '400011112222',
        propertyType: 'Flat',
        energyRating: 'A',
        floorArea: 200.75,
    );

    $roundtripped = AddressResult::fromArray($original->toArray());

    expect($roundtripped->fullAddress)->toBe($original->fullAddress)
        ->and($roundtripped->addressLine1)->toBe($original->addressLine1)
        ->and($roundtripped->addressLine2)->toBe($original->addressLine2)
        ->and($roundtripped->postcode)->toBe($original->postcode)
        ->and($roundtripped->uprn)->toBe($original->uprn)
        ->and($roundtripped->propertyType)->toBe($original->propertyType)
        ->and($roundtripped->energyRating)->toBe($original->energyRating)
        ->and($roundtripped->floorArea)->toBe($original->floorArea);
});

test('AddressResult roundtrips with all optional fields null', function () {
    $original = new AddressResult(
        fullAddress: '1 Test Road, London, SW1A 1AA',
        addressLine1: '1 Test Road',
        addressLine2: null,
        postcode: 'SW1A 1AA',
        uprn: null,
        propertyType: null,
        energyRating: null,
        floorArea: null,
    );

    $array = $original->toArray();
    $roundtripped = AddressResult::fromArray($array);

    expect($roundtripped->addressLine2)->toBeNull()
        ->and($roundtripped->uprn)->toBeNull()
        ->and($roundtripped->propertyType)->toBeNull()
        ->and($roundtripped->energyRating)->toBeNull()
        ->and($roundtripped->floorArea)->toBeNull();
});
