<?php

use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

function authValidFaceSignature(): array
{
    return array_map(
        static fn (int $index): float => round((($index % 17) - 8.5) / 40, 6),
        range(0, 127)
    );
}

function authFaceScanSession(string $token = 'scan-token'): array
{
    return [
        'face_scan_token_hash' => hash('sha256', $token),
        'face_scan_token_issued_at' => now()->timestamp,
    ];
}

function authFaceEnrollmentSession(User $user, string $token = 'scan-token'): array
{
    return authFaceScanSession($token) + [
        'face_enrollment_user_id' => $user->id,
    ];
}

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $this->withoutMiddleware(ValidateCsrfToken::class);

    $user = User::factory()->create();

    $response = $this
        ->withSession([
            'face_enrollment_user_id' => $user->id,
            'face_verified_at' => now()->toISOString(),
            'face_verified_user_id' => $user->id,
        ])
        ->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('face.verification', absolute: false));
    $response->assertSessionMissing('face_enrollment_user_id');
    $response->assertSessionMissing('face_verified_at');
    $response->assertSessionMissing('face_verified_user_id');
});

test('authenticated users must scan face before dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertRedirect(route('face.verification'));
});

test('stale face verified session without matching user is rejected', function () {
    $user = User::factory()->create();

    $response = $this
        ->withSession(['face_verified_at' => now()->toISOString()])
        ->actingAs($user)
        ->get('/dashboard');

    $response->assertRedirect(route('face.verification'));
    $response->assertSessionMissing('face_verified_at');
});

test('face verification screen can be rendered', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->get(route('face.verification'))
        ->assertOk()
        ->assertSee('window.FaceVerification', false)
        ->assertSee('face-verification', false);
});

test('users can access dashboard after face scan confirmation', function () {
    $this->withoutMiddleware(ValidateCsrfToken::class);

    $signature = authValidFaceSignature();
    $user = User::factory()->create(['face_signature' => $signature]);

    $response = $this
        ->withSession(authFaceScanSession())
        ->actingAs($user)
        ->post(route('face.verification.confirm'), [
            'face_scan_token' => 'scan-token',
            'face_signature' => json_encode($signature),
        ]);

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertNotNull(session('face_verified_at'));
    expect(session('face_verified_user_id'))->toBe($user->id);
    expect($user->fresh()->face_signature['template'])->toBe($signature);
});

test('face scan stores first valid signature only during enrollment', function () {
    $this->withoutMiddleware(ValidateCsrfToken::class);

    $user = User::factory()->create();

    $this
        ->withSession(authFaceEnrollmentSession($user))
        ->actingAs($user)
        ->post(route('face.verification.confirm'), [
            'face_scan_token' => 'scan-token',
            'face_signature' => json_encode(authValidFaceSignature()),
        ])
        ->assertRedirect(route('dashboard', absolute: false));

    $freshUser = $user->fresh();

    expect($freshUser->face_signature['template'])->toBe(authValidFaceSignature())
        ->and($freshUser->getRawOriginal('face_signature'))->not->toContain(json_encode(authValidFaceSignature()));
});

test('face scan does not enroll a normal login without a saved face', function () {
    $this->withoutMiddleware(ValidateCsrfToken::class);

    $user = User::factory()->create();

    $this
        ->withSession(authFaceScanSession())
        ->actingAs($user)
        ->from(route('face.verification'))
        ->post(route('face.verification.confirm'), [
            'face_scan_token' => 'scan-token',
            'face_signature' => json_encode(authValidFaceSignature()),
        ])
        ->assertRedirect(route('face.verification'))
        ->assertSessionHasErrors('face');

    $this->assertNull(session('face_verified_at'));
    expect($user->fresh()->face_signature)->toBeNull();
});

test('face scan rejects mismatched saved signature', function () {
    $this->withoutMiddleware(ValidateCsrfToken::class);

    $savedSignature = authValidFaceSignature();
    $mismatchedSignature = array_map(static fn (float $value): float => -$value, $savedSignature);
    $user = User::factory()->create(['face_signature' => $savedSignature]);

    $this
        ->withSession(authFaceScanSession())
        ->actingAs($user)
        ->from(route('face.verification'))
        ->post(route('face.verification.confirm'), [
            'face_scan_token' => 'scan-token',
            'face_signature' => json_encode($mismatchedSignature),
        ])
        ->assertRedirect(route('face.verification'))
        ->assertSessionHasErrors('face');

    $this->assertNull(session('face_verified_at'));
    expect($user->fresh()->face_signature)->toBe($savedSignature);
});

test('face scan rejects lookalike signatures outside strict distance', function () {
    $this->withoutMiddleware(ValidateCsrfToken::class);

    $savedSignature = authValidFaceSignature();
    $lookalikeSignature = array_map(
        static fn (float $value): float => round($value + 0.045, 6),
        $savedSignature
    );
    $user = User::factory()->create(['face_signature' => $savedSignature]);

    $this
        ->withSession(authFaceScanSession())
        ->actingAs($user)
        ->from(route('face.verification'))
        ->post(route('face.verification.confirm'), [
            'face_scan_token' => 'scan-token',
            'face_signature' => json_encode($lookalikeSignature),
        ])
        ->assertRedirect(route('face.verification'))
        ->assertSessionHasErrors('face');

    $this->assertNull(session('face_verified_at'));
    expect($user->fresh()->face_signature)->toBe($savedSignature);
});

test('face scan rejects unusable signatures', function () {
    $this->withoutMiddleware(ValidateCsrfToken::class);

    $user = User::factory()->create();

    $this
        ->withSession(authFaceScanSession())
        ->actingAs($user)
        ->from(route('face.verification'))
        ->post(route('face.verification.confirm'), [
            'face_scan_token' => 'scan-token',
            'face_signature' => json_encode(array_fill(0, 128, 0)),
        ])
        ->assertRedirect(route('face.verification'))
        ->assertSessionHasErrors('face');

    $this->assertNull(session('face_verified_at'));
    expect($user->fresh()->face_signature)->toBeNull();
});

test('users can not authenticate with invalid password', function () {
    $this->withoutMiddleware(ValidateCsrfToken::class);

    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $this->withoutMiddleware(ValidateCsrfToken::class);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
