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

test('marketing page contains why housescout section', function () {
    $response = $this->get('/');

    $response->assertSee('Why HouseScout?');
    $response->assertSee('One Search, Complete Picture');
    $response->assertSee('Personal Checklist');
    $response->assertSee('Official Sources Only');
});

test('marketing page footer links to real routes', function () {
    $response = $this->get('/');

    $response->assertSee(route('about'));
    $response->assertSee(route('pricing'));
    $response->assertSee(route('privacy'));
    $response->assertSee(route('terms'));
});

test('about page loads successfully', function () {
    $response = $this->get('/about');

    $response->assertStatus(200);
    $response->assertSee('About HouseScout');
});

test('pricing page loads successfully', function () {
    $response = $this->get('/pricing');

    $response->assertStatus(200);
    $response->assertSee('Simple, Transparent Pricing');
    $response->assertSee('Free');
    $response->assertSee('Pro');
    $response->assertSee('Team');
});

test('privacy page loads successfully', function () {
    $response = $this->get('/privacy');

    $response->assertStatus(200);
    $response->assertSee('Privacy Policy');
});

test('terms page loads successfully', function () {
    $response = $this->get('/terms');

    $response->assertStatus(200);
    $response->assertSee('Terms of Service');
});
