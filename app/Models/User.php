<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    public const ROLE_USER = 'user';
    public const ROLE_ADMIN = 'admin';

    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
        'provider',
        'provider_id',
        'email_verified_at',
        'last_seen_at',
        'face_signature',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'face_signature',
    ];

    /**
     * Задачи пользователя.
     */
    public function tasks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

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
            'last_seen_at' => 'datetime',
        ];
    }

    /**
     * Store the biometric template encrypted, while still allowing old JSON
     * templates to be read so existing users are not locked out.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<array<mixed>|null, array<mixed>|string|null>
     */
    protected function faceSignature(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value): ?array {
                if ($value === null || $value === '') {
                    return null;
                }

                if (is_array($value)) {
                    return $value;
                }

                try {
                    $decrypted = Crypt::decryptString((string) $value);
                    $decoded = json_decode($decrypted, true);

                    return is_array($decoded) ? $decoded : null;
                } catch (DecryptException) {
                    $decoded = json_decode((string) $value, true);

                    return is_array($decoded) ? $decoded : null;
                }
            },
            set: function (mixed $value): ?string {
                if ($value === null || $value === '') {
                    return null;
                }

                $encoded = json_encode($value, JSON_THROW_ON_ERROR);

                return Crypt::encryptString($encoded);
            },
        );
    }

    /**
     * Check if user is currently online (visited within last 5 minutes)
     */
    public function isOnline(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->diffInMinutes(now()) < 5;
    }
    
}
