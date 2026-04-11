<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DepartementSeeder::class,
            FiliereSeeder::class,
            CoursSeeder::class,
            SessionExamenSeeder::class,
            UtilisateurSeeder::class,
            RequeteSeeder::class,
        ]);
    }
}
