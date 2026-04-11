<?php

namespace Database\Seeders;

use App\Models\Cours;
use App\Models\Filiere;
use Illuminate\Database\Seeder;

class CoursSeeder extends Seeder
{
    private array $coursParFiliere = [
        'Gestion Systèmes d\'Information' => [
            1 => ['Algorithmique', 'Base de données', 'Systèmes d\'exploitation', 'Réseaux informatiques', 'Programmation web', 'Mathématiques appliquées', 'Anglais technique', 'Comptabilité générale'],
            2 => ['Programmation orientée objet', 'Administration système', 'Sécurité informatique', 'Développement mobile', 'Gestion de projet', 'Architecture des SI', 'Big Data', 'Stage professionnel'],
        ],
        'Marketing Commerce Vente' => [
            1 => ['Marketing fondamental', 'Techniques de vente', 'Communication commerciale', 'Droit commercial', 'Économie générale', 'Anglais commercial', 'Informatique bureautique', 'Statistiques'],
            2 => ['Marketing digital', 'Gestion de la relation client', 'Merchandising', 'Commerce international', 'Management commercial', 'Étude de marché', 'Publicité et communication', 'Stage commercial'],
        ],
        'Comptabilité Gestion Entreprises' => [
            1 => ['Comptabilité générale', 'Mathématiques financières', 'Droit des affaires', 'Économie d\'entreprise', 'Informatique comptable', 'Anglais des affaires', 'Fiscalité', 'Statistiques'],
            2 => ['Comptabilité analytique', 'Contrôle de gestion', 'Finance d\'entreprise', 'Audit comptable', 'Gestion budgétaire', 'Droit fiscal', 'Comptabilité des sociétés', 'Stage comptabilité'],
        ],
        'Action Commerciale' => [
            1 => ['Techniques de prospection', 'Négociation commerciale', 'Gestion des stocks', 'Logistique commerciale', 'Marketing de base', 'Anglais', 'Informatique', 'Économie'],
            2 => ['Gestion d\'équipe commerciale', 'E-commerce', 'Trade marketing', 'Gestion des approvisionnements', 'CRM', 'Management', 'Développement commercial', 'Stage'],
        ],
        'Banque' => [
            1 => ['Opérations bancaires', 'Droit bancaire', 'Mathématiques financières', 'Économie monétaire', 'Comptabilité', 'Anglais bancaire', 'Informatique', 'Fiscalité'],
            2 => ['Crédit bancaire', 'Finance internationale', 'Marchés financiers', 'Risques bancaires', 'Monnaie et politique monétaire', 'Audit bancaire', 'Banque islamique', 'Stage banque'],
        ],
        'Assistant Manager' => [
            1 => ['Management des organisations', 'Secrétariat de direction', 'Communication professionnelle', 'Droit du travail', 'Informatique de gestion', 'Anglais professionnel', 'Comptabilité', 'Ressources humaines'],
            2 => ['Gestion administrative', 'Management de projet', 'Veille stratégique', 'Négociation', 'Organisation et méthodes', 'Psychologie du travail', 'Système d\'information de gestion', 'Stage management'],
        ],
        'Gestion Ressources Humaines' => [
            1 => ['Fondamentaux RH', 'Droit du travail', 'Psychologie du travail', 'Recrutement et sélection', 'Communication RH', 'Anglais', 'Informatique RH', 'Sociologie des organisations'],
            2 => ['Gestion des carrières', 'Formation professionnelle', 'Paie et administration', 'Relations sociales', 'Évaluation des performances', 'GPEC', 'Management interculturel', 'Stage RH'],
        ],
    ];

    public function run(): void
    {
        $filieres = Filiere::all();

        foreach ($filieres as $filiere) {
            $nomFiliere = $filiere->nom_filiere;
            if (! isset($this->coursParFiliere[$nomFiliere])) {
                continue;
            }

            foreach ($this->coursParFiliere[$nomFiliere] as $niveau => $listeCours) {
                foreach ($listeCours as $nomCours) {
                    Cours::create([
                        'filiere_id' => $filiere->id,
                        'nom_cours'  => $nomCours,
                        'niveau'     => $niveau,
                        'credits'    => rand(2, 4),
                    ]);
                }
            }
        }
    }
}
