<?php

namespace Database\Seeders;

use App\Models\Administrateur;
use App\Models\AgentScolarite;
use App\Models\Etudiant;
use App\Models\Filiere;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UtilisateurSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::create([
            'nom'      => 'Djambou',
            'prenom'   => 'Steve',
            'email'    => 'admin@iug.cm',
            'password' => Hash::make('Admin@1234'),
            'role'     => 'admin',
            'est_actif' => true,
        ]);
        Administrateur::create(['utilisateur_id' => $admin->id, 'niveau_acces' => 2]);

        // Agents
        $agentsData = [
            ['nom' => 'Mvondo', 'prenom' => 'Paul',    'email' => 'agent1@iug.cm', 'matricule_agent' => 'AGT001'],
            ['nom' => 'Ntolo',  'prenom' => 'Marie',   'email' => 'agent2@iug.cm', 'matricule_agent' => 'AGT002'],
        ];
        foreach ($agentsData as $a) {
            $user = User::create([
                'nom'      => $a['nom'],
                'prenom'   => $a['prenom'],
                'email'    => $a['email'],
                'password' => Hash::make('Agent@1234'),
                'role'     => 'agent',
                'est_actif' => true,
            ]);
            AgentScolarite::create([
                'utilisateur_id' => $user->id,
                'matricule_agent' => $a['matricule_agent'],
                'service'        => 'Scolarité',
            ]);
        }

        // Étudiants
        $filieres = Filiere::all();
        $etudiantsData = [
            ['nom' => 'Kamdem', 'prenom' => 'Arthur',   'matricule' => '22G00001', 'niveau' => 1, 'filiere_idx' => 0],
            ['nom' => 'Njoya',  'prenom' => 'Ibrahim',  'matricule' => '22G00002', 'niveau' => 2, 'filiere_idx' => 0],
            ['nom' => 'Tchana', 'prenom' => 'Marie',    'matricule' => '22G00003', 'niveau' => 1, 'filiere_idx' => 1],
            ['nom' => 'Bello',  'prenom' => 'Fatima',   'matricule' => '22G00004', 'niveau' => 2, 'filiere_idx' => 2],
            ['nom' => 'Moussa', 'prenom' => 'Jean',     'matricule' => '22G00005', 'niveau' => 1, 'filiere_idx' => 3],
        ];

        foreach ($etudiantsData as $e) {
            $prenom = strtolower($e['prenom']);
            $nom    = strtolower($e['nom']);
            $user = User::create([
                'nom'      => $e['nom'],
                'prenom'   => $e['prenom'],
                'email'    => "{$prenom}.{$nom}@esg.cm",
                'password' => Hash::make('Etudiant@1234'),
                'role'     => 'etudiant',
                'est_actif' => true,
            ]);
            Etudiant::create([
                'utilisateur_id' => $user->id,
                'matricule'      => $e['matricule'],
                'filiere_id'     => $filieres[$e['filiere_idx']]->id,
                'niveau'         => $e['niveau'],
            ]);
        }
    }
}
