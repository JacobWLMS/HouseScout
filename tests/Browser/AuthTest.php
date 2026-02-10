<?php

use App\Models\User;
use Laravel\Dusk\Browser;

test('login page renders form', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/app/login')
            ->assertSee('Sign in')
            ->assertPresent('input[type="email"]')
            ->assertPresent('input[type="password"]');
    });
});

test('register page renders form', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/app/register')
            ->assertSee('Sign up')
            ->assertPresent('input[type="email"]')
            ->assertPresent('input[type="password"]');
    });
});

test('unauthenticated user redirected to login from dashboard', function () {
    $this->browse(function (Browser $browser) {
        $browser->logout()
            ->visit('/app')
            ->waitForLocation('/app/login')
            ->assertPathIs('/app/login');
    });
});

test('user can login and see dashboard', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $this->browse(function (Browser $browser) use ($user) {
        $browser->logout()
            ->visit('/app/login')
            ->waitFor('input[type="email"]')
            ->typeSlowly('input[type="email"]', $user->email)
            ->typeSlowly('input[type="password"]', 'password')
            ->press('Sign in')
            ->waitForLocation('/app')
            ->assertPathIs('/app');
    });
});

test('login with invalid credentials shows error', function () {
    $this->browse(function (Browser $browser) {
        $browser->logout()
            ->visit('/app/login')
            ->waitFor('input[type="email"]')
            ->typeSlowly('input[type="email"]', 'nonexistent@example.com')
            ->typeSlowly('input[type="password"]', 'wrongpassword')
            ->press('Sign in')
            ->waitForText('These credentials do not match our records')
            ->assertSee('These credentials do not match our records');
    });
});
