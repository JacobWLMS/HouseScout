<?php

test('marketing page loads successfully', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('marketing page contains hero headline', function () {
    $response = $this->get('/');

    $response->assertSee('Know Your Next Home Inside Out');
});

test('marketing page contains login link', function () {
    $response = $this->get('/');

    $response->assertSee('/app/login');
});

test('marketing page contains register link', function () {
    $response = $this->get('/');

    $response->assertSee('/app/register');
});

test('marketing page contains feature cards', function () {
    $response = $this->get('/');

    $response->assertSee('EPC Ratings');
    $response->assertSee('Planning History');
    $response->assertSee('Flood Risk');
    $response->assertSee('Crime Statistics');
    $response->assertSee('Land Registry');
    $response->assertSee('Demand Tracking');
});

test('marketing page contains how it works section', function () {
    $response = $this->get('/');

    $response->assertSee('Search');
    $response->assertSee('Analyse');
    $response->assertSee('Decide');
});
