<?php

namespace Database\Seeders;

use App\Models\Departement;
use Illuminate\Database\Seeder;

class DepartementSeeder extends Seeder
{
    public function run(): void
    {
        Departement::insert([
            ['nom_dep' => 'Informatique et Systèmes d\'Information', 'description' => 'Département ISI', 'created_at' => now(), 'updated_at' => now()],
            ['nom_dep' => 'Commerce et Gestion', 'description' => 'Département Commerce et Gestion', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
