<?php

namespace Database\Seeders;

use App\Models\SessionExamen;
use Illuminate\Database\Seeder;

class SessionExamenSeeder extends Seeder
{
    public function run(): void
    {
        $sessions = [
            ['code' => 'SN1',      'libelle' => 'Session Normale 1er semestre',  'annee_acad' => '2025-2026', 'est_active' => false, 'date_publication' => null],
            ['code' => 'SN2',      'libelle' => 'Session Normale 2ème semestre', 'annee_acad' => '2025-2026', 'est_active' => true,  'date_publication' => now()->subHours(48)],
            ['code' => 'RATT-SN1', 'libelle' => 'Rattrapage SN1',                'annee_acad' => '2025-2026', 'est_active' => false, 'date_publication' => null],
            ['code' => 'RATT-SN2', 'libelle' => 'Rattrapage SN2',                'annee_acad' => '2025-2026', 'est_active' => false, 'date_publication' => null],
            ['code' => 'CC1',      'libelle' => 'Contrôle Continu 1',            'annee_acad' => '2025-2026', 'est_active' => false, 'date_publication' => null],
            ['code' => 'CC2',      'libelle' => 'Contrôle Continu 2',            'annee_acad' => '2025-2026', 'est_active' => false, 'date_publication' => null],
            ['code' => 'BTS-BLANC','libelle' => 'BTS Blanc',                     'annee_acad' => '2025-2026', 'est_active' => false, 'date_publication' => null],
        ];

        foreach ($sessions as $s) {
            SessionExamen::create(array_merge($s, ['created_at' => now(), 'updated_at' => now()]));
        }
    }
}
