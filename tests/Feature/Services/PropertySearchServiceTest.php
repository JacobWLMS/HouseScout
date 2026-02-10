<?php

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

test('search creates property from valid postcode', function () {
    $user = User::factory()->create();

    $property = $this->service->search($user, 'SW1A 1AA');

    expect($property)->toBeInstanceOf(Property::class)
        ->and($property->postcode)->toBe('SW1A 1AA');
});

test('search reuses existing property for same postcode', function () {
    $user = User::factory()->create();
    $existing = Property::factory()->create(['postcode' => 'SW1A 1AA']);

    $property = $this->service->search($user, 'sw1a1aa');

    expect($property->id)->toBe($existing->id);
});

test('search logs the search in property_searches', function () {
    $user = User::factory()->create();

    $this->service->search($user, 'EC1A 1BB');

    expect(PropertySearch::query()->where('user_id', $user->id)->count())->toBe(1);
});

test('search dispatches FetchPropertyDataJob', function () {
    $user = User::factory()->create();

    $this->service->search($user, 'W1A 0AX');

    Queue::assertPushed(FetchPropertyDataJob::class);
});

test('search throws InvalidPostcodeException for non-postcode queries with no matches', function () {
    $user = User::factory()->create();

    expect(fn () => $this->service->search($user, 'non-existent address'))
        ->toThrow(InvalidPostcodeException::class);
});

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
