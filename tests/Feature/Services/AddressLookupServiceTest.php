<?php

use App\DataObjects\AddressResult;
use App\Exceptions\EpcApiKeyMissingException;
use App\Services\AddressLookupService;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config(['housescout.api.epc.key' => 'test-api-key', 'housescout.api.epc.email' => 'test@example.com']);
    $this->service = new AddressLookupService;
});

test('searchByPostcode returns addresses from EPC API', function () {
    Http::fake([
        '*/domestic/search*' => Http::response([
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

    $results = $this->service->searchByPostcode('SW1A 2AA');

    expect($results)->toHaveCount(2)
        ->and($results->first())->toBeInstanceOf(AddressResult::class)
        ->and($results->first()->propertyType)->toBe('House');
});

test('searchByPostcode deduplicates by UPRN keeping most recent', function () {
    Http::fake([
        '*/domestic/search*' => Http::response([
            'rows' => [
                [
                    'address' => '10, DOWNING STREET',
                    'uprn' => '100023336956',
                    'property-type' => 'House',
                    'current-energy-rating' => 'D',
                    'total-floor-area' => '100',
                    'lodgement-date' => '2018-01-01',
                ],
                [
                    'address' => '10, DOWNING STREET',
                    'uprn' => '100023336956',
                    'property-type' => 'House',
                    'current-energy-rating' => 'C',
                    'total-floor-area' => '100',
                    'lodgement-date' => '2022-06-15',
                ],
            ],
        ]),
    ]);

    $results = $this->service->searchByPostcode('SW1A 2AA');

    expect($results)->toHaveCount(1)
        ->and($results->first()->energyRating)->toBe('C');
});

test('searchByPostcode returns empty collection on API failure', function () {
    Http::fake([
        '*/domestic/search*' => Http::response(null, 500),
    ]);

    $results = $this->service->searchByPostcode('SW1A 2AA');

    expect($results)->toBeEmpty();
});

test('searchByPostcode throws EpcApiKeyMissingException when API key not configured', function () {
    config(['housescout.api.epc.key' => null]);

    $this->service->searchByPostcode('SW1A 2AA');
})->throws(EpcApiKeyMissingException::class);

test('searchByPostcode throws EpcApiKeyMissingException when API key is empty string', function () {
    config(['housescout.api.epc.key' => '']);

    $this->service->searchByPostcode('SW1A 2AA');
})->throws(EpcApiKeyMissingException::class);

test('searchByPostcode parses addresses correctly', function () {
    Http::fake([
        '*/domestic/search*' => Http::response([
            'rows' => [
                [
                    'address' => '10, DOWNING STREET, LONDON',
                    'uprn' => '100023336956',
                    'property-type' => 'House',
                    'current-energy-rating' => 'D',
                    'total-floor-area' => '100',
                    'lodgement-date' => '2020-06-15',
                ],
            ],
        ]),
    ]);

    $results = $this->service->searchByPostcode('SW1A 2AA');
    $address = $results->first();

    expect($address->addressLine1)->toBe('10 Downing Street')
        ->and($address->addressLine2)->toBe('London')
        ->and($address->fullAddress)->toBe('10, DOWNING STREET, LONDON');
});

test('searchByPostcode handles empty rows', function () {
    Http::fake([
        '*/domestic/search*' => Http::response([
            'rows' => [],
        ]),
    ]);

    $results = $this->service->searchByPostcode('SW1A 2AA');

    expect($results)->toBeEmpty();
});
