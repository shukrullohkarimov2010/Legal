<?php

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $this->withoutMiddleware(ValidateCsrfToken::class);

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test-'.strtolower(Str::random(10)).'@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('face.verification', absolute: false));
    expect(session('face_enrollment_user_id'))->toBe(Auth::id());
});
