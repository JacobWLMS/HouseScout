<?php

use App\DataObjects\AddressResult;
use App\Exceptions\EpcApiKeyMissingException;
use App\Services\AddressLookupService;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config(['housescout.api.epc.key' => 'test-api-key', 'housescout.api.epc.email' => 'test@example.com']);
    $this->service = new AddressLookupService;
});

test('parses address with flat number correctly', function () {
    Http::fake([
        '*/domestic/search*' => Http::response([
            'rows' => [
                [
                    'address' => 'Flat 1, 10, HIGH STREET, LONDON',
                    'uprn' => '100000000001',
                    'property-type' => 'Flat',
                    'current-energy-rating' => 'C',
                    'total-floor-area' => '55',
                    'lodgement-date' => '2022-01-01',
                ],
            ],
        ]),
    ]);

    $results = $this->service->searchByPostcode('SW1A 2AA');

    expect($results)->toHaveCount(1)
        ->and($results->first())->toBeInstanceOf(AddressResult::class)
        ->and($results->first()->fullAddress)->toBe('Flat 1, 10, HIGH STREET, LONDON');
});

test('parses address with just a name', function () {
    Http::fake([
        '*/domestic/search*' => Http::response([
            'rows' => [
                [
                    'address' => 'THE COTTAGE',
                    'uprn' => '100000000002',
                    'property-type' => 'House',
                    'current-energy-rating' => 'E',
                    'total-floor-area' => '80',
                    'lodgement-date' => '2020-05-01',
                ],
            ],
        ]),
    ]);

    $results = $this->service->searchByPostcode('OX1 1BX');

    expect($results)->toHaveCount(1)
        ->and($results->first()->addressLine1)->toBe('The Cottage')
        ->and($results->first()->addressLine2)->toBeNull();
});

test('handles mixed rows with and without UPRN', function () {
    Http::fake([
        '*/domestic/search*' => Http::response([
            'rows' => [
                [
                    'address' => '1, HIGH STREET',
                    'uprn' => '100000000001',
                    'property-type' => 'House',
                    'current-energy-rating' => 'C',
                    'total-floor-area' => '100',
                    'lodgement-date' => '2022-01-01',
                ],
                [
                    'address' => '2, HIGH STREET',
                    'uprn' => '',
                    'property-type' => 'House',
                    'current-energy-rating' => 'D',
                    'total-floor-area' => '90',
                    'lodgement-date' => '2021-06-01',
                ],
                [
                    'address' => '3, HIGH STREET',
                    'uprn' => '100000000003',
                    'property-type' => 'Flat',
                    'current-energy-rating' => 'B',
                    'total-floor-area' => '60',
                    'lodgement-date' => '2023-03-15',
                ],
            ],
        ]),
    ]);

    $results = $this->service->searchByPostcode('SW1A 2AA');

    expect($results)->toHaveCount(3);
});

test('handles large result sets with 20+ rows', function () {
    $rows = [];
    for ($i = 1; $i <= 25; $i++) {
        $rows[] = [
            'address' => "{$i}, LONG STREET",
            'uprn' => (string) (100000000000 + $i),
            'property-type' => 'House',
            'current-energy-rating' => 'C',
            'total-floor-area' => '100',
            'lodgement-date' => '2022-01-01',
        ];
    }

    Http::fake([
        '*/domestic/search*' => Http::response(['rows' => $rows]),
    ]);

    $results = $this->service->searchByPostcode('SW1A 2AA');

    expect($results)->toHaveCount(25);
});

test('sends size parameter to fetch all results from EPC API', function () {
    Http::fake([
        '*/domestic/search*' => Http::response(['rows' => []]),
    ]);

    $this->service->searchByPostcode('SW1A 2AA');

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'size=5000');
    });
});

test('handles rows with missing optional fields', function () {
    Http::fake([
        '*/domestic/search*' => Http::response([
            'rows' => [
                [
                    'address' => '10, MAIN STREET',
                    'uprn' => '100000000001',
                    'lodgement-date' => '2022-01-01',
                ],
            ],
        ]),
    ]);

    $results = $this->service->searchByPostcode('SW1A 2AA');

    expect($results)->toHaveCount(1)
        ->and($results->first()->propertyType)->toBeNull()
        ->and($results->first()->energyRating)->toBeNull()
        ->and($results->first()->floorArea)->toBeNull();
});

test('deduplicates multiple lodgement dates for same UPRN keeping most recent', function () {
    Http::fake([
        '*/domestic/search*' => Http::response([
            'rows' => [
                [
                    'address' => '10, MAIN STREET',
                    'uprn' => '100000000001',
                    'property-type' => 'House',
                    'current-energy-rating' => 'E',
                    'total-floor-area' => '95',
                    'lodgement-date' => '2015-03-01',
                ],
                [
                    'address' => '10, MAIN STREET',
                    'uprn' => '100000000001',
                    'property-type' => 'House',
                    'current-energy-rating' => 'C',
                    'total-floor-area' => '95',
                    'lodgement-date' => '2022-06-15',
                ],
                [
                    'address' => '10, MAIN STREET',
                    'uprn' => '100000000001',
                    'property-type' => 'House',
                    'current-energy-rating' => 'D',
                    'total-floor-area' => '95',
                    'lodgement-date' => '2019-09-20',
                ],
            ],
        ]),
    ]);

    $results = $this->service->searchByPostcode('SW1A 2AA');

    expect($results)->toHaveCount(1)
        ->and($results->first()->energyRating)->toBe('C');
});

test('parses address with many comma-separated parts', function () {
    Http::fake([
        '*/domestic/search*' => Http::response([
            'rows' => [
                [
                    'address' => 'FLAT 2, BLOCK A, THE TOWERS, 100, HIGH STREET, WESTMINSTER, LONDON',
                    'uprn' => '100000000001',
                    'property-type' => 'Flat',
                    'current-energy-rating' => 'B',
                    'total-floor-area' => '65',
                    'lodgement-date' => '2023-01-15',
                ],
            ],
        ]),
    ]);

    $results = $this->service->searchByPostcode('SW1A 2AA');

    expect($results)->toHaveCount(1)
        ->and($results->first()->addressLine1)->not->toBeEmpty()
        ->and($results->first()->addressLine2)->not->toBeNull();
});

test('handles empty address string', function () {
    Http::fake([
        '*/domestic/search*' => Http::response([
            'rows' => [
                [
                    'address' => '',
                    'uprn' => '100000000001',
                    'property-type' => 'House',
                    'current-energy-rating' => 'D',
                    'total-floor-area' => '100',
                    'lodgement-date' => '2022-01-01',
                ],
            ],
        ]),
    ]);

    $results = $this->service->searchByPostcode('SW1A 2AA');

    expect($results)->toHaveCount(1)
        ->and($results->first()->addressLine1)->toBe('Unknown Address');
});

test('handles HTTP timeout gracefully', function () {
    Http::fake(fn () => throw new \Illuminate\Http\Client\ConnectionException('Connection timed out'));

    $results = $this->service->searchByPostcode('SW1A 2AA');

    expect($results)->toBeEmpty();
});

test('handles 401 unauthorized response', function () {
    Http::fake([
        '*/domestic/search*' => Http::response(['error' => 'Unauthorized'], 401),
    ]);

    $results = $this->service->searchByPostcode('SW1A 2AA');

    expect($results)->toBeEmpty();
});

test('handles 429 rate limited response', function () {
    Http::fake([
        '*/domestic/search*' => Http::response(['error' => 'Rate limit exceeded'], 429),
    ]);

    $results = $this->service->searchByPostcode('SW1A 2AA');

    expect($results)->toBeEmpty();
});

test('throws EpcApiKeyMissingException when API key is null', function () {
    config(['housescout.api.epc.key' => null]);

    expect(fn () => $this->service->searchByPostcode('SW1A 2AA'))
        ->toThrow(EpcApiKeyMissingException::class);
});

test('throws EpcApiKeyMissingException when API key is empty string', function () {
    config(['housescout.api.epc.key' => '']);

    expect(fn () => $this->service->searchByPostcode('SW1A 2AA'))
        ->toThrow(EpcApiKeyMissingException::class);
});

test('results are sorted by addressLine1', function () {
    Http::fake([
        '*/domestic/search*' => Http::response([
            'rows' => [
                [
                    'address' => '30, ZEBRA LANE',
                    'uprn' => '100000000003',
                    'property-type' => 'House',
                    'current-energy-rating' => 'C',
                    'total-floor-area' => '100',
                    'lodgement-date' => '2022-01-01',
                ],
                [
                    'address' => '10, APPLE ROAD',
                    'uprn' => '100000000001',
                    'property-type' => 'House',
                    'current-energy-rating' => 'D',
                    'total-floor-area' => '90',
                    'lodgement-date' => '2022-01-01',
                ],
                [
                    'address' => '20, MAPLE AVENUE',
                    'uprn' => '100000000002',
                    'property-type' => 'Flat',
                    'current-energy-rating' => 'B',
                    'total-floor-area' => '60',
                    'lodgement-date' => '2022-01-01',
                ],
            ],
        ]),
    ]);

    $results = $this->service->searchByPostcode('SW1A 2AA');

    expect($results->first()->addressLine1)->toBe('10 Apple Road')
        ->and($results->last()->addressLine1)->toBe('30 Zebra Lane');
});
