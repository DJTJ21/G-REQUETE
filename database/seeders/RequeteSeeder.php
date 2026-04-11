<?php

namespace Database\Seeders;

use App\Enums\StatutRequete;
use App\Enums\TypeAnomalie;
use App\Models\AgentScolarite;
use App\Models\Cours;
use App\Models\Etudiant;
use App\Models\Note;
use App\Models\Requete;
use App\Models\SessionExamen;
use Illuminate\Database\Seeder;

class RequeteSeeder extends Seeder
{
    public function run(): void
    {
        $session = SessionExamen::where('code', 'SN2')->first();
        $agent   = AgentScolarite::first();
        $etudiants = Etudiant::with('filiere')->get();

        $statuts = [
            StatutRequete::EnAttente->value,
            StatutRequete::EnCoursVerification->value,
            StatutRequete::TraiteeFondee->value,
            StatutRequete::TraiteeNonFondee->value,
        ];

        $types = [
            TypeAnomalie::NoteAbsente->value,
            TypeAnomalie::NoteErronee->value,
            TypeAnomalie::IncoherenceCcSn->value,
            TypeAnomalie::ErreurTranscription->value,
        ];

        $counter = 1;
        foreach ($etudiants as $etudiant) {
            $coursList = Cours::where('filiere_id', $etudiant->filiere_id)
                ->where('niveau', $etudiant->niveau)
                ->inRandomOrder()
                ->take(3)
                ->get();

            foreach ($coursList as $cours) {
                $statut = $statuts[array_rand($statuts)];
                $type   = $types[array_rand($types)];
                $ref    = 'REQ-' . now()->format('Ymd') . '-' . str_pad($counter, 4, '0', STR_PAD_LEFT);

                $requete = Requete::create([
                    'ref_requete'          => $ref,
                    'etudiant_id'          => $etudiant->id,
                    'cours_id'             => $cours->id,
                    'session_id'           => $session->id,
                    'agent_id'             => in_array($statut, [StatutRequete::EnCoursVerification->value, StatutRequete::TraiteeFondee->value, StatutRequete::TraiteeNonFondee->value]) ? $agent->id : null,
                    'type_anomalie'        => $type,
                    'description'          => 'Requête de test générée par le seeder pour le cours ' . $cours->nom_cours . '.',
                    'statut'               => $statut,
                    'motif_rejet'          => $statut === StatutRequete::TraiteeNonFondee->value ? 'La note saisie est correcte après vérification du procès-verbal d\'examen.' : null,
                    'date_soumission'      => now()->subDays(rand(1, 10)),
                    'date_prise_en_charge' => in_array($statut, [StatutRequete::EnCoursVerification->value, StatutRequete::TraiteeFondee->value, StatutRequete::TraiteeNonFondee->value]) ? now()->subDays(rand(1, 5)) : null,
                    'date_traitement'      => in_array($statut, [StatutRequete::TraiteeFondee->value, StatutRequete::TraiteeNonFondee->value]) ? now()->subDays(rand(1, 3)) : null,
                ]);

                if ($statut === StatutRequete::TraiteeFondee->value) {
                    Note::create([
                        'requete_id'       => $requete->id,
                        'etudiant_id'      => $etudiant->id,
                        'cours_id'         => $cours->id,
                        'note_avant'       => round(rand(0, 8) + rand(0, 9) / 10, 2),
                        'note_apres'       => round(rand(10, 18) + rand(0, 9) / 10, 2),
                        'modifie_par'      => $agent->id,
                        'date_modification' => now()->subDays(rand(1, 3)),
                    ]);
                }

                $counter++;
                if ($counter > 15) break 2;
            }
        }
    }
}
