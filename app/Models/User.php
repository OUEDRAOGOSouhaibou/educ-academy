<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Les attributs assignables en masse.
     * NOTE DE SÉCURITÉ : 'role' et 'status' sont strictement EXCLUS du $fillable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'must_change_password',
        'approved_at',
    ];

    /**
     * Valeurs par défaut des attributs du modèle.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'role' => 'student',
        'status' => 'pending',
        'must_change_password' => false,
    ];

    /**
     * Les attributs masqués pour la sérialisation.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Les conversions d'attributs.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => UserRole::class,
            'status' => UserStatus::class,
            'must_change_password' => 'boolean',
            'approved_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Normalisation du numéro de téléphone (suppression des espaces, tirets et points).
     */
    public static function normalizePhone(?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }

        return preg_replace('/[\s\-\.]/', '', trim($phone));
    }

    /**
     * Mutateur automatique pour toujours stocker le téléphone sous sa forme normalisée.
     */
    protected function phone(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => self::normalizePhone($value)
        );
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isStudent(): bool
    {
        return $this->role === UserRole::Student;
    }

    public function isPending(): bool
    {
        return $this->status === UserStatus::Pending;
    }

    public function isApproved(): bool
    {
        return $this->status === UserStatus::Approved;
    }

    public function isBlocked(): bool
    {
        return $this->status === UserStatus::Blocked;
    }
}
