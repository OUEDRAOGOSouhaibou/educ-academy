<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Student = 'student';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrateur',
            self::Student => 'Élève',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Admin => 'bg-danger',
            self::Student => 'bg-primary',
        };
    }
}

