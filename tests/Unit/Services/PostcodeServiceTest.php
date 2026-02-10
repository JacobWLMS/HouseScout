<?php

use App\Services\PostcodeService;

beforeEach(function () {
    $this->service = new PostcodeService;
});

test('validates correct UK postcodes', function (string $postcode) {
    expect($this->service->validate($postcode))->toBeTrue();
})->with([
    'SW1A 1AA',
    'EC1A 1BB',
    'W1A 0AX',
    'M1 1AE',
    'B33 8TH',
    'CR2 6XH',
    'DN55 1PT',
    'sw1a1aa',
    'sw1a 1aa',
    '  SW1A 1AA  ',
]);

test('rejects invalid postcodes', function (string $postcode) {
    expect($this->service->validate($postcode))->toBeFalse();
})->with([
    '',
    'INVALID',
    '12345',
    'SW1A',
    'AAA 1AA',
    'SW1A 1A',
]);

test('normalizes postcodes to uppercase with proper spacing', function (string $input, string $expected) {
    expect($this->service->normalize($input))->toBe($expected);
})->with([
    ['sw1a1aa', 'SW1A 1AA'],
    ['SW1A1AA', 'SW1A 1AA'],
    ['sw1a 1aa', 'SW1A 1AA'],
    ['  ec1a  1bb  ', 'EC1A 1BB'],
    ['m11ae', 'M1 1AE'],
    ['B338TH', 'B33 8TH'],
]);

test('extracts outcode from postcode', function (string $postcode, string $expected) {
    expect($this->service->extractOutcode($postcode))->toBe($expected);
})->with([
    ['SW1A 1AA', 'SW1A'],
    ['sw1a1aa', 'SW1A'],
    ['M1 1AE', 'M1'],
    ['B33 8TH', 'B33'],
    ['EC1A 1BB', 'EC1A'],
]);

test('extracts incode from postcode', function (string $postcode, string $expected) {
    expect($this->service->extractIncode($postcode))->toBe($expected);
})->with([
    ['SW1A 1AA', '1AA'],
    ['sw1a1aa', '1AA'],
    ['M1 1AE', '1AE'],
    ['B33 8TH', '8TH'],
    ['EC1A 1BB', '1BB'],
]);
