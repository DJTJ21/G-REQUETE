<?php

namespace App\Enums;

enum StatutRequete: string
{
    case EnAttente = 'en_attente';
    case EnCoursVerification = 'en_cours_verification';
    case TraiteeFondee = 'traitee_fondee';
    case TraiteeNonFondee = 'traitee_non_fondee';

    public function label(): string
    {
        return match($this) {
            self::EnAttente => 'En attente',
            self::EnCoursVerification => 'En cours de vérification',
            self::TraiteeFondee => 'Traitée et fondée',
            self::TraiteeNonFondee => 'Traitée et non fondée',
        };
    }

    public function couleur(): string
    {
        return match($this) {
            self::EnAttente => 'amber',
            self::EnCoursVerification => 'blue',
            self::TraiteeFondee => 'green',
            self::TraiteeNonFondee => 'red',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::EnAttente => 'badge-amber',
            self::EnCoursVerification => 'badge-blue',
            self::TraiteeFondee => 'badge-green',
            self::TraiteeNonFondee => 'badge-red',
        };
    }
}
