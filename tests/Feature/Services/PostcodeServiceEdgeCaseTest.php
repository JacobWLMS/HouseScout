<?php

use App\Services\PostcodeService;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->service = new PostcodeService;
});

// --- autocomplete edge cases ---

test('autocomplete trims leading and trailing whitespace', function () {
    Http::fake([
        'api.postcodes.io/postcodes/SW1A/autocomplete' => Http::response([
            'status' => 200,
            'result' => ['SW1A 0AA'],
        ]),
    ]);

    $results = $this->service->autocomplete('  SW1A  ');

    expect($results)->toBe(['SW1A 0AA']);
});

test('autocomplete with special characters returns empty array on 404', function () {
    Http::fake([
        'api.postcodes.io/*' => Http::response(null, 404),
    ]);

    $results = $this->service->autocomplete('SW&1#');

    expect($results)->toBe([]);
});

test('autocomplete returns empty array when API returns 200 with empty result array', function () {
    Http::fake([
        'api.postcodes.io/postcodes/ZZ10/autocomplete' => Http::response([
            'status' => 200,
            'result' => [],
        ]),
    ]);

    $results = $this->service->autocomplete('ZZ10');

    expect($results)->toBe([]);
});

test('autocomplete handles connection timeout gracefully', function () {
    Http::fake(fn () => throw new \Illuminate\Http\Client\ConnectionException('Connection timed out'));

    $results = $this->service->autocomplete('SW1A');

    expect($results)->toBe([]);
});

// --- lookup edge cases ---

test('lookup trims whitespace from postcode', function () {
    Http::fake([
        'api.postcodes.io/*' => Http::response([
            'status' => 200,
            'result' => [
                'postcode' => 'SW1A 1AA',
                'latitude' => 51.501,
                'longitude' => -0.1416,
                'admin_district' => 'Westminster',
                'admin_county' => null,
            ],
        ]),
    ]);

    $result = $this->service->lookup('  SW1A 1AA  ');

    expect($result)->not->toBeNull()
        ->and($result->postcode)->toBe('SW1A 1AA');
});

test('lookup returns null when API returns 200 but result is null', function () {
    Http::fake([
        'api.postcodes.io/*' => Http::response([
            'status' => 200,
            'result' => null,
        ]),
    ]);

    $result = $this->service->lookup('ZZ99 9ZZ');

    expect($result)->toBeNull();
});

test('lookup handles incomplete result with missing optional fields', function () {
    Http::fake([
        'api.postcodes.io/*' => Http::response([
            'status' => 200,
            'result' => [
                'postcode' => 'M1 1AE',
                'latitude' => 53.4808,
                'longitude' => -2.2426,
            ],
        ]),
    ]);

    $result = $this->service->lookup('M1 1AE');

    expect($result)->not->toBeNull()
        ->and($result->postcode)->toBe('M1 1AE')
        ->and($result->adminDistrict)->toBeNull()
        ->and($result->adminCounty)->toBeNull();
});

test('lookup handles connection timeout gracefully', function () {
    Http::fake(fn () => throw new \Illuminate\Http\Client\ConnectionException('Connection timed out'));

    $result = $this->service->lookup('SW1A 1AA');

    expect($result)->toBeNull();
});

// --- normalize with various UK postcode formats ---

test('normalize handles all UK postcode formats', function (string $input, string $expected) {
    expect($this->service->normalize($input))->toBe($expected);
})->with([
    'A9 9AA format' => ['M1 1AE', 'M1 1AE'],
    'A99 9AA format' => ['M60 1NW', 'M60 1NW'],
    'A9A 9AA format' => ['W1A 0AX', 'W1A 0AX'],
    'AA9 9AA format' => ['LS1 1UR', 'LS1 1UR'],
    'AA99 9AA format' => ['DN55 1PT', 'DN55 1PT'],
    'AA9A 9AA format' => ['EC1A 1BB', 'EC1A 1BB'],
    'no space' => ['SW1A1AA', 'SW1A 1AA'],
    'lowercase' => ['sw1a 1aa', 'SW1A 1AA'],
    'extra spaces' => ['  EC1A  1BB  ', 'EC1A 1BB'],
]);

// --- extractOutcode with various formats ---

test('extractOutcode handles all UK postcode formats', function (string $input, string $expected) {
    expect($this->service->extractOutcode($input))->toBe($expected);
})->with([
    'A9 format' => ['M1 1AE', 'M1'],
    'A99 format' => ['M60 1NW', 'M60'],
    'A9A format' => ['W1A 0AX', 'W1A'],
    'AA9 format' => ['LS1 1UR', 'LS1'],
    'AA99 format' => ['DN55 1PT', 'DN55'],
    'AA9A format' => ['EC1A 1BB', 'EC1A'],
]);

// --- extractIncode with various formats ---

test('extractIncode handles all UK postcode formats', function (string $input, string $expected) {
    expect($this->service->extractIncode($input))->toBe($expected);
})->with([
    'A9 format' => ['M1 1AE', '1AE'],
    'A99 format' => ['M60 1NW', '1NW'],
    'A9A format' => ['W1A 0AX', '0AX'],
    'AA9 format' => ['LS1 1UR', '1UR'],
    'AA99 format' => ['DN55 1PT', '1PT'],
    'AA9A format' => ['EC1A 1BB', '1BB'],
]);

// --- validate with various formats ---

test('validate accepts all UK postcode format types', function (string $postcode) {
    expect($this->service->validate($postcode))->toBeTrue();
})->with([
    'A9 9AA' => 'M1 1AE',
    'A99 9AA' => 'M60 1NW',
    'A9A 9AA' => 'W1A 0AX',
    'AA9 9AA' => 'LS1 1UR',
    'AA99 9AA' => 'DN55 1PT',
    'AA9A 9AA' => 'EC1A 1BB',
]);
