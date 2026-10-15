<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'username',
        'carnet',
        'email',
        'correo_secundario',
        'photo',
        'password',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Resuelve a la cuenta por usuario, o por correo (principal o
     * secundario) cuando ese correo identifica a UNA sola cuenta. Los
     * correos principales compartidos por camión son ambiguos: ahí solo
     * sirve el usuario o el correo secundario personal.
     */
    public static function findByIdentifier(string $identifier): ?self
    {
        $identifier = trim($identifier);

        if ($user = static::where('username', $identifier)->first()) {
            return $user;
        }

        if ($user = static::where('correo_secundario', $identifier)->first()) {
            return $user;
        }

        $byEmail = static::where('email', $identifier)->limit(2)->get();

        return $byEmail->count() === 1 ? $byEmail->first() : null;
    }

    /**
     * Correo donde llega el código de recuperación: el secundario, o el
     * principal si no está compartido con otra cuenta.
     */
    public function recoveryEmail(): ?string
    {
        if ($this->correo_secundario) {
            return $this->correo_secundario;
        }

        $shared = static::where('email', $this->email)->where('id', '!=', $this->id)->exists();

        return $shared ? null : $this->email;
    }

    const ROLE_LABELS = [
        'superadmin'       => 'Superadmin',
        'administracion'   => 'Administración',
        'operador'         => 'Operador',
        'mecanico'         => 'Mecánico',
        'mecanico_externo' => 'Mecánico Externo',
    ];

    public function getRoleLabelAttribute(): string
    {
        $role = $this->roles->first()?->name;

        return self::ROLE_LABELS[$role] ?? 'Sin rol';
    }
}
