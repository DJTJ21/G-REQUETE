<?php

namespace Database\Seeders;

use App\Models\Departement;
use App\Models\Filiere;
use Illuminate\Database\Seeder;

class FiliereSeeder extends Seeder
{
    public function run(): void
    {
        $isi = Departement::where('nom_dep', 'like', '%Informatique%')->first();
        $cg  = Departement::where('nom_dep', 'like', '%Commerce%')->first();

        $filieres = [
            ['departement_id' => $isi->id, 'nom_filiere' => 'Gestion Systèmes d\'Information', 'cycle' => 'BTS'],
            ['departement_id' => $cg->id,  'nom_filiere' => 'Marketing Commerce Vente', 'cycle' => 'BTS'],
            ['departement_id' => $cg->id,  'nom_filiere' => 'Comptabilité Gestion Entreprises', 'cycle' => 'BTS'],
            ['departement_id' => $cg->id,  'nom_filiere' => 'Action Commerciale', 'cycle' => 'BTS'],
            ['departement_id' => $cg->id,  'nom_filiere' => 'Banque', 'cycle' => 'BTS'],
            ['departement_id' => $cg->id,  'nom_filiere' => 'Assistant Manager', 'cycle' => 'BTS'],
            ['departement_id' => $cg->id,  'nom_filiere' => 'Gestion Ressources Humaines', 'cycle' => 'BTS'],
        ];

        foreach ($filieres as $f) {
            Filiere::create(array_merge($f, ['created_at' => now(), 'updated_at' => now()]));
        }
    }
}
