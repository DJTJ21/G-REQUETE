<?php

namespace App\Services;

use App\Models\AgentScolarite;
use App\Models\Requete;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StatistiquesService
{
    public function requetesParStatut(): array
    {
        return Requete::select('statut', DB::raw('count(*) as total'))
            ->groupBy('statut')
            ->pluck('total', 'statut')
            ->toArray();
    }

    public function requetesParMois(int $annee): array
    {
        $data = Requete::select(
            DB::raw('MONTH(date_soumission) as mois'),
            DB::raw('count(*) as total')
        )
            ->whereYear('date_soumission', $annee)
            ->groupBy('mois')
            ->orderBy('mois')
            ->pluck('total', 'mois')
            ->toArray();

        $result = [];
        for ($m = 1; $m <= 12; $m++) {
            $result[$m] = $data[$m] ?? 0;
        }

        return $result;
    }

    public function tauxTraitement(): float
    {
        $total   = Requete::count();
        $traites = Requete::whereIn('statut', ['traitee_fondee', 'traitee_non_fondee'])->count();

        return $total > 0 ? round(($traites / $total) * 100, 1) : 0;
    }

    public function topAgents(int $limit = 5): \Illuminate\Support\Collection
    {
        return AgentScolarite::withCount(['requetes as requetes_traitees' => function ($q) {
            $q->whereIn('statut', ['traitee_fondee', 'traitee_non_fondee']);
        }])
            ->orderByDesc('requetes_traitees')
            ->limit($limit)
            ->get();
    }

    public function statsGlobales(): array
    {
        return [
            'total_etudiants'  => User::where('role', 'etudiant')->count(),
            'total_agents'     => User::where('role', 'agent')->count(),
            'total_requetes'   => Requete::count(),
            'en_attente'       => Requete::where('statut', 'en_attente')->count(),
            'en_cours'         => Requete::where('statut', 'en_cours_verification')->count(),
            'traitees_fondees' => Requete::where('statut', 'traitee_fondee')->count(),
            'traitees_non_fondees' => Requete::where('statut', 'traitee_non_fondee')->count(),
            'taux_traitement'  => $this->tauxTraitement(),
        ];
    }
}
