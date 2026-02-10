<?php

use App\DataObjects\PostcodeLookupResult;
use App\Services\PostcodeService;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->service = new PostcodeService;
});

test('autocomplete returns postcode suggestions', function () {
    Http::fake([
        'api.postcodes.io/postcodes/SW1A/autocomplete' => Http::response([
            'status' => 200,
            'result' => ['SW1A 0AA', 'SW1A 0PW', 'SW1A 1AA'],
        ]),
    ]);

    $results = $this->service->autocomplete('SW1A');

    expect($results)->toBe(['SW1A 0AA', 'SW1A 0PW', 'SW1A 1AA']);
});

test('autocomplete returns empty array for short input', function () {
    $results = $this->service->autocomplete('S');

    expect($results)->toBe([]);
});

test('autocomplete returns empty array on api failure', function () {
    Http::fake([
        'api.postcodes.io/*' => Http::response(null, 500),
    ]);

    $results = $this->service->autocomplete('SW1A');

    expect($results)->toBe([]);
});

test('autocomplete returns empty array when result is null', function () {
    Http::fake([
        'api.postcodes.io/postcodes/ZZ99/autocomplete' => Http::response([
            'status' => 200,
            'result' => null,
        ]),
    ]);

    $results = $this->service->autocomplete('ZZ99');

    expect($results)->toBe([]);
});

test('lookup returns PostcodeLookupResult for valid postcode', function () {
    Http::fake([
        'api.postcodes.io/*' => Http::response([
            'status' => 200,
            'result' => [
                'postcode' => 'SW1A 1AA',
                'latitude' => 51.501,
                'longitude' => -0.1416,
                'admin_district' => 'Westminster',
                'admin_county' => null,
                'lsoa' => 'Westminster 023C',
                'msoa' => 'Westminster 023',
                'admin_ward' => "St James's",
                'parliamentary_constituency' => 'Cities of London and Westminster',
                'eastings' => 529090,
                'northings' => 179645,
            ],
        ]),
    ]);

    $result = $this->service->lookup('SW1A 1AA');

    expect($result)->toBeInstanceOf(PostcodeLookupResult::class)
        ->and($result->postcode)->toBe('SW1A 1AA')
        ->and($result->latitude)->toBe(51.501)
        ->and($result->longitude)->toBe(-0.1416)
        ->and($result->adminDistrict)->toBe('Westminster')
        ->and($result->adminCounty)->toBeNull()
        ->and($result->lsoa)->toBe('Westminster 023C')
        ->and($result->msoa)->toBe('Westminster 023')
        ->and($result->ward)->toBe("St James's")
        ->and($result->constituency)->toBe('Cities of London and Westminster')
        ->and($result->easting)->toBe(529090)
        ->and($result->northing)->toBe(179645)
        ->and($result->localAuthority)->toBe('Westminster');
});

test('lookup returns null for invalid postcode', function () {
    Http::fake([
        'api.postcodes.io/*' => Http::response(null, 404),
    ]);

    $result = $this->service->lookup('INVALID');

    expect($result)->toBeNull();
});

test('lookup returns null on api failure', function () {
    Http::fake([
        'api.postcodes.io/*' => Http::response(null, 500),
    ]);

    $result = $this->service->lookup('SW1A 1AA');

    expect($result)->toBeNull();
});
