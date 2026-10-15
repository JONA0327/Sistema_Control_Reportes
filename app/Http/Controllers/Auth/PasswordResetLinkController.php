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
            'username' => ['required', 'string'],
        ]);

        $user = User::findByIdentifier($request->string('username')->toString());

        if (! $user) {
            return back()->withInput()->withErrors([
                'username' => 'No encontramos una cuenta con ese usuario o correo. Si tu correo es compartido, usa tu usuario o tu correo secundario.',
            ]);
        }

        $recoveryEmail = $user->recoveryEmail();

        if (! $recoveryEmail) {
            return back()->withInput()->withErrors([
                'username' => 'Esta cuenta comparte correo y no tiene un correo secundario registrado para recuperar la contraseña. Contacta a un administrador.',
            ]);
        }

        PasswordResetCode::where('user_id', $user->id)->delete();

        $code = (string) random_int(100000, 999999);
        $resetCode = PasswordResetCode::create([
            'user_id' => $user->id,
            'email' => $recoveryEmail,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(15),
        ]);

        $user->notify(new PasswordResetCodeNotification($code));

        return back()->with([
            'status' => 'Te enviamos un código de seguridad a tu correo de recuperación.',
            'code_sent' => true,
            'reset_username' => $user->username,
        ]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'code' => ['required', 'digits:6'],
        ]);

        // El formulario reenvía el usuario real (reset_username) tras pedir el código.
        $resetCode = PasswordResetCode::whereHas('user', fn ($q) => $q->where('username', $request->string('username')->toString()))
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
