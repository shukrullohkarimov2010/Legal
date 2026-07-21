<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);
        // Try to send the password reset link. We intentionally do not reveal
        // whether the email exists in the system to avoid leaking user data.
        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            if ($this->usesLocalOnlyMailer()) {
                return back()->with('status', 'Ссылка сброса создана, но MAIL_MAILER=' . config('mail.default') . ': письмо не уходит на Gmail, а сохраняется локально. Настройте SMTP в .env или смотрите storage/logs/laravel.log.');
            }

            return back()->with('status', __('ui.password_reset_sent'));
        }

        logger()->debug('Password reset link was not sent', [
            'email' => $request->input('email'),
            'status' => $status,
        ]);

        if ($this->canShowPasswordResetDiagnostics()) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => $this->passwordResetStatusMessage($status)]);
        }

        // Production keeps the same response to avoid leaking user data.
        return back()->with('status', __('ui.password_reset_sent'));
    }

    private function usesLocalOnlyMailer(): bool
    {
        return $this->canShowPasswordResetDiagnostics()
            && in_array(config('mail.default'), ['array', 'log'], true);
    }

    private function canShowPasswordResetDiagnostics(): bool
    {
        return app()->environment(['local', 'testing']);
    }

    private function passwordResetStatusMessage(string $status): string
    {
        return match ($status) {
            Password::INVALID_USER => 'Пользователь с таким email не найден. Зарегистрируйтесь или укажите email существующего аккаунта.',
            Password::RESET_THROTTLED => 'Ссылка уже запрошена недавно. Подождите немного и попробуйте снова.',
            default => 'Не удалось отправить ссылку для сброса пароля. Проверьте email и настройки почты.',
        };
    }
}
