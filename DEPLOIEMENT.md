# Déploiement G-REQUETES avec Docker

> **Prérequis :** Docker Desktop installé (Windows/Mac/Linux)  
> 👉 https://www.docker.com/products/docker-desktop/

---

## 3 étapes pour démarrer

### Étape 1 — Préparer le dossier

Créer un dossier `grequetes/` n'importe où sur la machine et y placer **2 fichiers** :

**`docker-compose.yml`** → copier le contenu de `docker-compose.prod.yml` de ce repo  
**`.env`** → copier le contenu de `.env.production.example` de ce repo

> Sur Windows : créer `C:\grequetes\docker-compose.yml` et `C:\grequetes\.env`

Ouvrir le fichier `.env` et modifier uniquement les lignes marquées `***` :
```
APP_URL=http://localhost:8080     ← adresse de la machine si accès réseau
DB_PASSWORD=MonMotDePasse         ← choisir un mot de passe
DB_ROOT_PASSWORD=MonRootPass      ← choisir un mot de passe
MAIL_HOST=smtp.iug.cm             ← serveur mail de l'IUG
MAIL_USERNAME=...
MAIL_PASSWORD=...
```

---

### Étape 2 — Lancer

```bash
docker compose up -d
```

Docker télécharge automatiquement depuis Docker Hub :
- `djrrm01/g-requete-app:prod` — application Laravel (~250 Mo)
- `mysql:8.0` — base de données
- `redis:alpine` — cache

**Au premier démarrage, l'application fait automatiquement :**
- ✅ Migrations de la base de données
- ✅ Création des comptes de test (seed)
- ✅ Génération de la clé APP_KEY
- ✅ Optimisation des caches

> ⏳ Première initialisation : **60 à 90 secondes**

---

### Étape 3 — Accéder

Ouvrir le navigateur : **http://localhost:8080**

---

## Comptes de test (créés automatiquement)

| Rôle | Identifiant | Mot de passe |
|---|---|---|
| **Administrateur** | `admin@iug.cm` | `Admin@1234` |
| **Agent scolarité** | `agent1@iug.cm` | `Agent@1234` |
| **Agent scolarité** | `agent2@iug.cm` | `Agent@1234` |
| **Étudiant** | `arthur.kamdem@esg.cm` | `Etudiant@1234` |
| **Étudiant** | `ibrahim.njoya@esg.cm` | `Etudiant@1234` |

> ⚠️ Changer ces mots de passe après la première connexion en production.

---

## Vérifier que tout tourne

```bash
docker compose ps
```

Résultat attendu :
```
NAME               STATUS
sygre_app          Up (healthy)
sygre_db           Up (healthy)
sygre_redis        Up
sygre_worker       Up
sygre_scheduler    Up
```

Voir les logs du démarrage :
```bash
docker logs sygre_app -f
```

---

## Accès réseau local (autres PC du bureau)

Trouver l'IP de la machine :
```bash
# Linux/Mac
ip a | grep inet

# Windows PowerShell
ipconfig | findstr IPv4
```

Accéder depuis un autre PC : `http://192.168.x.x:8080`

Ouvrir le port dans le pare-feu Windows (en administrateur) :
```powershell
netsh advfirewall firewall add rule name="G-REQUETES" dir=in action=allow protocol=TCP localport=8080
```

---

## Commandes utiles

```bash
# Arrêter (données conservées)
docker compose stop

# Redémarrer
docker compose start

# Voir les logs en temps réel
docker logs sygre_app -f

# Exécuter une commande Artisan
docker exec sygre_app php artisan <commande>

# Ré-exécuter le seed manuellement
docker exec sygre_app php artisan db:seed --force

# Backup de la base de données
docker exec sygre_db mysqldump -u sygre_user -p sygre_db > backup_$(date +%Y%m%d).sql

# Arrêter et tout supprimer (⚠️ perd les données)
docker compose down -v
```

---

## Mise à jour de l'application

Quand une nouvelle version est disponible sur Docker Hub :

```bash
# Arrêter l'app (la DB et Redis continuent)
docker compose stop app worker scheduler

# Tirer la nouvelle image
docker compose pull app

# Relancer (migrations appliquées automatiquement)
docker compose up -d app worker scheduler
```

---

## Dépannage

| Problème | Solution |
|---|---|
| Page blanche / erreur 500 | `docker logs sygre_app --tail=50` |
| "No encryption key" | `docker exec sygre_app php artisan key:generate --force` puis `docker compose restart app worker` |
| Port 8080 déjà utilisé | Changer `APP_PORT=9090` dans `.env` |
| MySQL ne démarre pas | `docker logs sygre_db --tail=30` — vérifier `DB_PASSWORD` dans `.env` |
| Réinitialiser complètement | `docker compose down -v` puis `docker compose up -d` |
