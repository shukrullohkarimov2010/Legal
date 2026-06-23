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

        // Log non-existent users at debug level for administrators (no user-facing leak)
        if ($status !== Password::RESET_LINK_SENT) {
            logger()->debug('Password reset requested for unknown email', ['email' => $request->input('email'), 'status' => $status]);
        }

        // Always return the same response to the user for security / UX consistency.
        return back()->with('status', __('ui.password_reset_sent'));
    }
}
