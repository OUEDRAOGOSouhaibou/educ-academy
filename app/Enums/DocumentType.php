<?php

namespace App\Enums;

enum DocumentType: string
{
    case Cours = 'cours';
    case Serie = 'serie';
    case Devoir = 'devoir';
    case Corrige = 'corrige';

    public function label(): string
    {
        return match ($this) {
            self::Cours => 'Cours',
            self::Serie => 'Série d\'exercices',
            self::Devoir => 'Devoir',
            self::Corrige => 'Corrigé',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Cours => 'bg-primary',
            self::Serie => 'bg-info text-dark',
            self::Devoir => 'bg-warning text-dark',
            self::Corrige => 'bg-success',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Cours => 'bi-journal-text',
            self::Serie => 'bi-list-check',
            self::Devoir => 'bi-pencil-square',
            self::Corrige => 'bi-check-circle',
        };
    }
}

