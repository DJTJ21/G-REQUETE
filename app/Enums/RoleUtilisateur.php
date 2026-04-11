<?php

namespace App\Enums;

enum RoleUtilisateur: string
{
    case Etudiant = 'etudiant';
    case Agent = 'agent';
    case Admin = 'admin';

    public function label(): string
    {
        return match($this) {
            self::Etudiant => 'Étudiant',
            self::Agent => 'Agent de Scolarité',
            self::Admin => 'Administrateur',
        };
    }
}
