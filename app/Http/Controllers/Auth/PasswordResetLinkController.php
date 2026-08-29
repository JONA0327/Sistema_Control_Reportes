<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetCode;
use App\Models\User;
use App\Notifications\PasswordResetCodeNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
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
    * Generate and email a single-use password reset code.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->string('email')->toString())->first();

        if (! $user) {
            return back()->withInput()->withErrors([
                'email' => 'No encontramos una cuenta con ese correo electrónico.',
            ]);
        }

        PasswordResetCode::where('email', $user->email)->delete();

        $code = (string) random_int(100000, 999999);
        $resetCode = PasswordResetCode::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(15),
        ]);

        $user->notify(new PasswordResetCodeNotification($code));

        return back()->with([
            'status' => 'Te enviamos un código de seguridad a tu correo electrónico.',
            'code_sent' => true,
            'reset_email' => $user->email,
        ]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:6'],
        ]);

        $resetCode = PasswordResetCode::where('email', $request->string('email')->toString())
            ->whereNull('used_at')
            ->latest()
            ->first();

        if (! $resetCode || $resetCode->expires_at->isPast() || ! Hash::check($request->string('code')->toString(), $resetCode->code_hash)) {
            return back()->withInput()->withErrors([
                'code' => 'El código no es válido o ya caducó.',
            ]);
        }

        $resetCode->update(['used_at' => now()]);

        $request->session()->put('password_reset_code_id', $resetCode->id);

        return redirect()->route('password.reset');
    }
}
