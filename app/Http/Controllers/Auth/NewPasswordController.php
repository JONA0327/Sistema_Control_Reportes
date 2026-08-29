<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetCode;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        abort_unless($request->session()->has('password_reset_code_id'), 403);

        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $resetCode = PasswordResetCode::with('user')
            ->whereKey($request->session()->get('password_reset_code_id'))
            ->whereNotNull('used_at')
            ->first();

        abort_unless($resetCode && $resetCode->used_at->gt(now()->subMinutes(15)), 403);

        $user = $resetCode->user;
        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();
        $resetCode->delete();
        $request->session()->forget('password_reset_code_id');
        event(new PasswordReset($user));

        return redirect()->route('login')->with('status', 'Tu contraseña se actualizó correctamente.');
    }
}
