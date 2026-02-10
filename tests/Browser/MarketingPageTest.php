<?php

use Laravel\Dusk\Browser;

test('marketing page loads and shows hero content', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->assertSee('HouseScout')
            ->assertSee('Know Your Next Home Inside Out')
            ->assertPresent('#theme-toggle');
    });
});

test('dark mode toggle switches theme', function () {
    $this->browse(function (Browser $browser) {
        // Clear localStorage and reload to start in light mode
        $browser->visit('/');
        $browser->script("localStorage.removeItem('theme')");
        $browser->visit('/');

        // Verify light mode (no dark class)
        $isDark = $browser->script("return document.documentElement.classList.contains('dark')");
        expect($isDark[0])->toBeFalse();

        // Click toggle
        $browser->click('#theme-toggle');
        $browser->pause(500);

        // Verify dark mode
        $isDark = $browser->script("return document.documentElement.classList.contains('dark')");
        expect($isDark[0])->toBeTrue();

        // Toggle back
        $browser->click('#theme-toggle');
        $browser->pause(500);

        $isDark = $browser->script("return document.documentElement.classList.contains('dark')");
        expect($isDark[0])->toBeFalse();
    });
});

test('navigation links are present', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->assertSeeLink('Login')
            ->assertSeeLink('Sign Up');
    });
});

test('login link navigates to login page', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->clickLink('Login')
            ->waitForLocation('/app/login')
            ->assertPathIs('/app/login');
    });
});

test('signup link navigates to register page', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->clickLink('Sign Up')
            ->waitForLocation('/app/register')
            ->assertPathIs('/app/register');
    });
});

test('marketing page shows features section', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->assertSee('Everything you need to know about a property')
            ->assertSee('EPC Ratings')
            ->assertSee('Planning History')
            ->assertSee('Flood Risk')
            ->assertSee('Crime Statistics')
            ->assertSee('Land Registry')
            ->assertSee('Demand Tracking');
    });
});

test('marketing page shows how it works section', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->assertSee('How it works')
            ->assertSee('Search')
            ->assertSee('Analyse')
            ->assertSee('Decide');
    });
});

test('marketing page shows footer', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->assertSee('Built with official UK government data sources.');
    });
});
