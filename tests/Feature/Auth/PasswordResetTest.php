<?php

namespace Tests\Feature\Auth;

use App\Models\PasswordResetCode;
use App\Models\User;
use App\Notifications\PasswordResetCodeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_code_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, PasswordResetCodeNotification::class);
        $this->assertDatabaseHas('password_reset_codes', ['email' => $user->email]);
    }

    public function test_unknown_email_does_not_reveal_account_existence_via_success(): void
    {
        $response = $this->post('/forgot-password', ['email' => 'nobody@example.com']);

        $response->assertSessionHasErrors('email');
    }

    public function test_valid_code_allows_password_reset(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, PasswordResetCodeNotification::class, function ($notification) use ($user) {
            $verify = $this->post('/verify-reset-code', [
                'email' => $user->email,
                'code' => $notification->code,
            ]);

            $verify->assertSessionHasNoErrors()->assertRedirect(route('password.reset'));

            $reset = $this->post('/reset-password', [
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

            $reset->assertSessionHasNoErrors()->assertRedirect(route('login'));

            $this->assertTrue($user->fresh()->password !== $user->password);

            return true;
        });
    }

    public function test_invalid_code_is_rejected(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        $response = $this->post('/verify-reset-code', [
            'email' => $user->email,
            'code' => '000000',
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_expired_code_is_rejected(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        PasswordResetCode::where('email', $user->email)->update([
            'expires_at' => now()->subMinute(),
        ]);

        Notification::assertSentTo($user, PasswordResetCodeNotification::class, function ($notification) use ($user) {
            $response = $this->post('/verify-reset-code', [
                'email' => $user->email,
                'code' => $notification->code,
            ]);

            $response->assertSessionHasErrors('code');

            return true;
        });
    }

    public function test_code_cannot_be_used_twice(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, PasswordResetCodeNotification::class, function ($notification) use ($user) {
            $this->post('/verify-reset-code', [
                'email' => $user->email,
                'code' => $notification->code,
            ])->assertSessionHasNoErrors();

            $this->post('/reset-password', [
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

            $replay = $this->post('/verify-reset-code', [
                'email' => $user->email,
                'code' => $notification->code,
            ]);

            $replay->assertSessionHasErrors('code');

            return true;
        });
    }
}
