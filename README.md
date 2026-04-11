# G-REQUÊTES — Système de Gestion des Requêtes Académiques

Plateforme web Laravel 11 permettant aux étudiants de soumettre des requêtes académiques (contestations de notes, anomalies d'examens) et aux agents de scolarité de les traiter avec génération de procès-verbaux PDF.

---

## Stack Technique

| Couche | Technologie |
|--------|-------------|
| Backend | Laravel 11 (PHP 8.2) |
| Frontend | Blade + Tailwind CSS 3 + Alpine.js 3 |
| Base de données | MySQL 8 |
| Cache / Queue | Redis |
| Mail dev | Mailpit |
| PDF | barryvdh/laravel-dompdf |
| Conteneurisation | Docker Compose (sans Sail) |

---

## Démarrage rapide

### Prérequis
- Docker Desktop ≥ 24 et Docker Compose v2
- Make (optionnel mais recommandé)

### 1. Cloner et configurer

```bash
git clone <repo-url> G-REQUETE
cd G-REQUETE
cp .env.example .env
```

### 2. Lancer les conteneurs

```bash
make up
# ou : docker compose up -d --build
```

L'entrypoint s'occupe automatiquement de :
- attendre MySQL,
- générer la clé applicative,
- exécuter les migrations,
- lancer les seeders,
- créer le lien symbolique `storage`.

### 3. Installer les dépendances front

```bash
make npm-install   # npm install
make npm-build     # npm run build
```

### 4. Accéder à l'application

| Service | URL |
|---------|-----|
| Application | http://localhost:8080 |
| Mailpit (webmail dev) | http://localhost:8025 |

---

## Comptes de test (seedés)

| Rôle | Email | Mot de passe |
|------|-------|-------------|
| Administrateur | admin@iug.cm | Admin@1234 |
| Agent scolarité | agent1@iug.cm | Agent@1234 |
| Agent scolarité | agent2@iug.cm | Agent@1234 |
| Étudiant | arthur.kamdem@esg.cm | Etudiant@1234 |
| Étudiant | ibrahim.njoya@esg.cm | Etudiant@1234 |

---

## Architecture

```
app/
├── Enums/              # RoleUtilisateur, StatutRequete, TypeAnomalie…
├── Exceptions/         # FenetreDepasseeException
├── Http/
│   ├── Controllers/    # AuthController, EtudiantController, AgentController,
│   │                   # AdminController, AdminReferentielController
│   ├── Middleware/     # EnsureIsEtudiant/Agent/Admin
│   └── Requests/       # SoumettreRequeteRequest, ChangerStatutRequest, GenererPVRequest
├── Jobs/               # EnvoyerEmailRequeteJob, EnvoyerEmailStatutJob
├── Mail/               # RequeteRecueMail, StatutChangeMail
├── Models/             # User, Etudiant, AgentScolarite, Requete, PvRequete…
└── Services/           # RequeteService, PVService, NotificationService,
                        # FichierService, StatistiquesService

resources/
├── css/app.css         # Tailwind + classes utilitaires custom
├── js/app.js           # Polling notifications, CSRF fetch, flash dismiss
└── views/
    ├── layouts/        # app.blade.php (sidebar), auth.blade.php
    ├── components/     # alert, badge-statut, card-stat, empty-state
    ├── auth/           # login.blade.php
    ├── etudiant/       # dashboard, requetes/*, notifications, profil
    ├── agent/          # dashboard, requetes/*, pv/*, historique, profil
    ├── admin/          # dashboard, utilisateurs/*, journal/*, statistiques
    ├── pdf/            # pv_requetes.blade.php (DomPDF)
    └── emails/         # requete-recue.blade.php, statut-change.blade.php
```

---

## Commandes Makefile

```bash
make up          # docker compose up -d --build
make down        # docker compose down
make logs        # logs du conteneur app
make shell       # bash dans le conteneur app
make artisan CMD="route:list"
make npm-install
make npm-dev
make npm-build
```

---

## Règles métier

- **Fenêtre de soumission** : 72h après la date de publication des résultats par session.
- **Rôles** : `etudiant` → `agent` → `admin`, chacun avec un espace dédié et des middlewares séparés.
- **Workflow requête** : `en_attente` → `en_cours_verification` → `traitee_fondee | traitee_non_fondee`.
- **PV** : généré en PDF (DomPDF, format A4 paysage) par l'agent à partir des requêtes traitées.
- **Notifications** : en temps réel (app) + email via queue Redis (`emails`).
- **Rate limiting** : 5 tentatives de connexion / minute par IP.

---

## Migrations & Seeders

```bash
# Reset complet avec seeders
docker compose exec app php artisan migrate:fresh --seed

# Seeders seuls
docker compose exec app php artisan db:seed
```

---

## Queue worker

Le service `worker` Docker démarre automatiquement `php artisan queue:work redis --queue=emails,default`.

Pour surveiller les jobs en local :

```bash
docker compose exec app php artisan queue:listen --queue=emails,default
```

---

## Licences

- Laravel — MIT
- Tailwind CSS — MIT
- Alpine.js — MIT
- barryvdh/laravel-dompdf — MIT
