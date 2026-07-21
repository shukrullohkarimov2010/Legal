<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FaceVerificationController extends Controller
{
    private const MIN_SIMILARITY = 0.90;
    private const MAX_EUCLIDEAN_DISTANCE = 0.45;
    private const MIN_STRONG_SIMILARITY = 0.96;
    private const MAX_STRONG_EUCLIDEAN_DISTANCE = 0.32;
    private const MAX_SAMPLE_EUCLIDEAN_DISTANCE = 0.35;
    private const MIN_SIGNATURE_DEVIATION = 0.01;
    private const SIGNATURE_SIZE = 128;
    private const MIN_CAPTURED_SAMPLES = 3;
    private const MAX_STORED_TEMPLATES = 4;
    private const SCAN_TOKEN_TTL_SECONDS = 600;

    public function show(Request $request): View|RedirectResponse
    {
        if ($this->hasVerifiedFaceSession($request)) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $token = Str::random(40);
        $request->session()->put('face_scan_token_hash', hash('sha256', $token));
        $request->session()->put('face_scan_token_issued_at', now()->timestamp);

        return view('auth.face-verification', [
            'faceScanToken' => $token,
        ]);
    }

    public function showPermission(Request $request): View|RedirectResponse
    {
        if ($this->hasVerifiedFaceSession($request)) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        if (! $request->session()->has('face_enrollment_user_id')) {
            return redirect(route('login', absolute: false));
        }

        return view('auth.face-permission');
    }

    public function permissionYes(Request $request): RedirectResponse
    {
        if (! $request->session()->has('face_enrollment_user_id')) {
            return redirect(route('login', absolute: false));
        }

        return redirect(route('face.verification', absolute: false));
    }

    public function permissionNo(Request $request): RedirectResponse
    {
        if (! $request->session()->has('face_enrollment_user_id')) {
            return redirect(route('login', absolute: false));
        }

        $request->session()->forget([
            'face_enrollment_user_id',
            'face_scan_token_hash',
            'face_scan_token_issued_at',
        ]);
        $request->session()->put('face_verified_user_id', Auth::id());
        $request->session()->put('face_verified_at', now()->toISOString());

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function confirm(Request $request): RedirectResponse
    {
        $request->validate([
            'face_scan_token' => ['required', 'string', 'max:80'],
            'face_signature' => ['required', 'string', 'max:50000'],
        ]);

        $this->assertValidScanToken($request);

        $payload = $this->decodeSignaturePayload((string) $request->input('face_signature'));
        $signature = $payload['signature'] ?? null;

        if ($signature === null) {
            return back()->withErrors([
                'face' => __('ui.face_scan_signature_error'),
            ]);
        }

        $user = Auth::user();
        $storedProfile = $this->decodeStoredFaceProfile($user->face_signature);

        if ($storedProfile !== null) {
            $match = $this->bestProfileMatch($storedProfile['templates'], $signature);

            if ($match['similarity'] < self::MIN_SIMILARITY || $match['distance'] > self::MAX_EUCLIDEAN_DISTANCE) {
                return back()->withErrors([
                    'face' => __('ui.face_scan_mismatch'),
                ]);
            }

            if ($match['similarity'] >= self::MIN_STRONG_SIMILARITY && $match['distance'] <= self::MAX_STRONG_EUCLIDEAN_DISTANCE) {
                $user->forceFill([
                    'face_signature' => $this->refreshFaceProfile($storedProfile, $signature),
                ])->save();
            }
        } elseif ($this->hasStoredSignature($user->getRawOriginal('face_signature')) || ! $this->canEnrollFace($request)) {
            return back()->withErrors([
                'face' => __('ui.face_scan_saved_signature_error'),
            ]);
        } else {
            $user->forceFill([
                'face_signature' => $this->createFaceProfile($signature, $payload['sample_count'] ?? 1),
            ])->save();
        }

        $request->session()->forget([
            'face_enrollment_user_id',
            'face_scan_token_hash',
            'face_scan_token_issued_at',
        ]);
        $request->session()->put('face_verified_user_id', Auth::id());
        $request->session()->put('face_verified_at', now()->toISOString());

        return redirect()->intended(route('dashboard', absolute: false));
    }

    private function hasVerifiedFaceSession(Request $request): bool
    {
        return $request->session()->has('face_verified_at')
            && (int) $request->session()->get('face_verified_user_id', 0) === (int) Auth::id();
    }

    private function assertValidScanToken(Request $request): void
    {
        $tokenHash = $request->session()->get('face_scan_token_hash');
        $issuedAt = (int) $request->session()->get('face_scan_token_issued_at', 0);
        $isExpired = $issuedAt <= 0 || now()->timestamp - $issuedAt > self::SCAN_TOKEN_TTL_SECONDS;

        abort_unless(
            is_string($tokenHash)
            && ! $isExpired
            && hash_equals($tokenHash, hash('sha256', (string) $request->input('face_scan_token'))),
            419
        );
    }

    /**
     * @return array{signature: array<int, float>|null, sample_count: int}
     */
    private function decodeSignaturePayload(string $value): array
    {
        $payload = json_decode($value, true);

        if (! is_array($payload)) {
            return ['signature' => null, 'sample_count' => 0];
        }

        $samples = [];
        if (isset($payload['samples']) && is_array($payload['samples'])) {
            foreach ($payload['samples'] as $sample) {
                $signature = $this->decodeSignature($sample);

                if ($signature !== null) {
                    $samples[] = $signature;
                }
            }
        }

        if (count($samples) >= self::MIN_CAPTURED_SAMPLES) {
            $averaged = $this->averageSignatures($samples);

            if ($this->samplesAreStable($samples, $averaged)) {
                return [
                    'signature' => $averaged,
                    'sample_count' => count($samples),
                ];
            }
        }

        $signature = $this->decodeSignature($payload['signature'] ?? $payload);

        return [
            'signature' => $signature,
            'sample_count' => $signature === null ? 0 : 1,
        ];
    }

    /**
     * @return array{templates: array<int, array<int, float>>, salt?: string, created_at?: string}|null
     */
    private function decodeStoredFaceProfile(mixed $value): ?array
    {
        $legacySignature = $this->decodeSignature($value);

        if ($legacySignature !== null) {
            return [
                'templates' => [$legacySignature],
            ];
        }

        if (! is_array($value)) {
            return null;
        }

        $template = $this->decodeSignature($value['template'] ?? null);
        if ($template === null) {
            return null;
        }

        $templates = [];
        if (isset($value['templates']) && is_array($value['templates'])) {
            foreach ($value['templates'] as $storedTemplate) {
                $signature = $this->decodeSignature($storedTemplate);

                if ($signature !== null) {
                    $templates[] = $signature;
                }
            }
        }

        if ($templates === []) {
            $templates[] = $template;
        }

        $salt = (string) ($value['salt'] ?? '');
        $checksum = (string) ($value['checksum'] ?? '');

        if ($salt !== '' && $checksum !== '' && ! hash_equals($checksum, $this->profileChecksum($template, $salt))) {
            return null;
        }

        return [
            'templates' => array_slice($templates, 0, self::MAX_STORED_TEMPLATES),
            'salt' => $salt,
            'created_at' => is_string($value['created_at'] ?? null) ? $value['created_at'] : now()->toISOString(),
        ];
    }

    /**
     * @param  array<int, float>  $signature
     * @return array<string, mixed>
     */
    private function createFaceProfile(array $signature, int $sampleCount): array
    {
        $salt = bin2hex(random_bytes(16));

        return [
            'version' => 3,
            'model' => 'face-api.js',
            'descriptor_size' => self::SIGNATURE_SIZE,
            'template' => $signature,
            'templates' => [$signature],
            'sample_count' => max(1, $sampleCount),
            'salt' => $salt,
            'checksum' => $this->profileChecksum($signature, $salt),
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString(),
            'last_verified_at' => now()->toISOString(),
        ];
    }

    /**
     * @param  array{templates: array<int, array<int, float>>, salt?: string, created_at?: string}  $profile
     * @param  array<int, float>  $signature
     * @return array<string, mixed>
     */
    private function refreshFaceProfile(array $profile, array $signature): array
    {
        $currentTemplate = $profile['templates'][0] ?? $signature;
        $refreshedTemplate = $this->blendSignatures($currentTemplate, $signature, 0.15);
        $salt = $profile['salt'] ?? bin2hex(random_bytes(16));
        $templates = [$refreshedTemplate, $signature];

        foreach ($profile['templates'] as $template) {
            if (count($templates) >= self::MAX_STORED_TEMPLATES) {
                break;
            }

            if ($this->euclideanDistance($template, $signature) > 0.08) {
                $templates[] = $template;
            }
        }

        return [
            'version' => 3,
            'model' => 'face-api.js',
            'descriptor_size' => self::SIGNATURE_SIZE,
            'template' => $refreshedTemplate,
            'templates' => array_slice($templates, 0, self::MAX_STORED_TEMPLATES),
            'salt' => $salt,
            'checksum' => $this->profileChecksum($refreshedTemplate, $salt),
            'created_at' => $profile['created_at'] ?? now()->toISOString(),
            'updated_at' => now()->toISOString(),
            'last_verified_at' => now()->toISOString(),
        ];
    }

    /**
     * @return array<int, float>|null
     */
    private function decodeSignature(mixed $value): ?array
    {
        $signature = is_string($value) ? json_decode($value, true) : $value;

        if (! is_array($signature) || count($signature) !== self::SIGNATURE_SIZE) {
            return null;
        }

        $decoded = [];

        foreach (array_values($signature) as $item) {
            if (! is_int($item) && ! is_float($item) && ! (is_string($item) && is_numeric($item))) {
                return null;
            }

            $decoded[] = max(-2.0, min(2.0, round((float) $item, 6)));
        }

        return $this->isUsableSignature($decoded) ? $decoded : null;
    }

    private function hasStoredSignature(mixed $value): bool
    {
        if (is_array($value)) {
            return count($value) > 0;
        }

        return is_string($value) && trim($value) !== '';
    }

    private function canEnrollFace(Request $request): bool
    {
        return (int) $request->session()->get('face_enrollment_user_id', 0) === (int) Auth::id();
    }

    /**
     * @param  array<int, array<int, int|float>>  $signatures
     * @return array<int, float>
     */
    private function averageSignatures(array $signatures): array
    {
        $averaged = [];

        for ($index = 0; $index < self::SIGNATURE_SIZE; $index++) {
            $sum = 0.0;

            foreach ($signatures as $signature) {
                $sum += $signature[$index] ?? 0;
            }

            $averaged[] = max(-2.0, min(2.0, round($sum / count($signatures), 6)));
        }

        return $averaged;
    }

    /**
     * @param  array<int, array<int, int|float>>  $samples
     * @param  array<int, int|float>  $averaged
     */
    private function samplesAreStable(array $samples, array $averaged): bool
    {
        foreach ($samples as $sample) {
            if ($this->euclideanDistance($sample, $averaged) > self::MAX_SAMPLE_EUCLIDEAN_DISTANCE) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<int, array<int, int|float>>  $templates
     * @param  array<int, int|float>  $signature
     * @return array{similarity: float, distance: float}
     */
    private function bestProfileMatch(array $templates, array $signature): array
    {
        $best = [
            'similarity' => -1.0,
            'distance' => INF,
        ];

        foreach ($templates as $template) {
            $similarity = $this->cosineSimilarity($template, $signature);
            $distance = $this->euclideanDistance($template, $signature);

            if ($similarity > $best['similarity'] || ($similarity === $best['similarity'] && $distance < $best['distance'])) {
                $best = [
                    'similarity' => $similarity,
                    'distance' => $distance,
                ];
            }
        }

        return $best;
    }

    /**
     * @param  array<int, int|float>  $base
     * @param  array<int, int|float>  $fresh
     * @return array<int, float>
     */
    private function blendSignatures(array $base, array $fresh, float $freshWeight): array
    {
        $blended = [];
        $baseWeight = 1.0 - $freshWeight;

        foreach ($base as $index => $baseValue) {
            $freshValue = $fresh[$index] ?? $baseValue;
            $blended[] = max(-2.0, min(2.0, round(($baseValue * $baseWeight) + ($freshValue * $freshWeight), 6)));
        }

        return $blended;
    }

    /**
     * @param  array<int, int|float>  $signature
     */
    private function profileChecksum(array $signature, string $salt): string
    {
        return hash_hmac(
            'sha256',
            $salt.'|'.json_encode(array_values($signature), JSON_THROW_ON_ERROR),
            (string) config('app.key')
        );
    }

    /**
     * @param  array<int, int|float>  $signature
     */
    private function isUsableSignature(array $signature): bool
    {
        $mean = array_sum($signature) / count($signature);
        $variance = 0.0;
        $nonZeroCount = 0;
        $magnitude = 0.0;

        foreach ($signature as $value) {
            $variance += ($value - $mean) ** 2;
            $magnitude += $value * $value;

            if (abs($value) >= 0.0001) {
                $nonZeroCount++;
            }
        }

        $magnitude = sqrt($magnitude);

        return sqrt($variance / count($signature)) >= self::MIN_SIGNATURE_DEVIATION
            && $nonZeroCount >= 80
            && $magnitude >= 0.35
            && $magnitude <= 3.0;
    }

    /**
     * @param  array<int, int|float>  $saved
     * @param  array<int, int|float>  $current
     */
    private function cosineSimilarity(array $saved, array $current): float
    {
        $dot = 0.0;
        $savedMagnitude = 0.0;
        $currentMagnitude = 0.0;

        foreach ($saved as $index => $savedValue) {
            $currentValue = $current[$index] ?? 0;
            $dot += $savedValue * $currentValue;
            $savedMagnitude += $savedValue * $savedValue;
            $currentMagnitude += $currentValue * $currentValue;
        }

        if ($savedMagnitude <= 0.0 || $currentMagnitude <= 0.0) {
            return 0.0;
        }

        return $dot / (sqrt($savedMagnitude) * sqrt($currentMagnitude));
    }

    /**
     * @param  array<int, int|float>  $saved
     * @param  array<int, int|float>  $current
     */
    private function euclideanDistance(array $saved, array $current): float
    {
        $distance = 0.0;

        foreach ($saved as $index => $savedValue) {
            $distance += ($savedValue - ($current[$index] ?? 0)) ** 2;
        }

        return sqrt($distance);
    }
}
