<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialUser;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialController extends Controller
{
    public function redirectToProvider(string $provider): RedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback(Request $request, string $provider): RedirectResponse
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Throwable $e) {
            Log::error('Social Auth Error', [
                'provider' => $provider,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('login')
                ->with('error', 'Не удалось выполнить вход через ' . ucfirst($provider) . '.');
        }

        $email = $socialUser->getEmail();
        if (!$email) {
            return redirect()
                ->route('login')
                ->with('error', 'Провайдер не вернул email-адрес.');
        }

        $user = User::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if (!$user) {
            $user = User::where('email', $email)->first();
        }

        $isNewUser = false;

        if (!$user) {
            $user = $this->createUserFromSocialProvider($provider, $socialUser, $email);
            $isNewUser = true;
        } else {
            $this->syncSocialProviderData($user, $provider, $socialUser);
        }

        Auth::login($user, true);
        $request->session()->regenerate();
        $request->session()->forget([
            'face_enrollment_user_id',
            'face_verified_at',
            'face_verified_user_id',
        ]);

        if ($isNewUser) {
            $request->session()->put('face_enrollment_user_id', $user->id);
        }

        return redirect()->intended(route('face.verification', absolute: false));
    }

    private function createUserFromSocialProvider(string $provider, SocialUser $socialUser, string $email): User
    {
        return User::create([
            'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
            'email' => $email,
            'password' => bcrypt(Str::random(32)),
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'email_verified_at' => now(),
        ]);
    }

    private function syncSocialProviderData(User $user, string $provider, SocialUser $socialUser): void
    {
        $updates = [];

        if (!$user->provider || !$user->provider_id) {
            $updates['provider'] = $provider;
            $updates['provider_id'] = $socialUser->getId();
        }

        if (!$user->email_verified_at) {
            $updates['email_verified_at'] = now();
        }

        if ($updates !== []) {
            $user->update($updates);
        }
    }
}
