# Guide de Déploiement — G-REQUÊTES sur Windows

> **Public cible :** administrateur système, machine Windows 10/11 64-bit  
> **Image Docker Hub :** `djtj21/g-requete-app:prod`  
> **Durée estimée :** 15 à 30 minutes

---

## Table des matières

1. [Prérequis Windows](#1-prérequis-windows)
2. [Préparer le dossier de déploiement](#2-préparer-le-dossier)
3. [Premier démarrage sur Windows](#4-premier-démarrage)
4. [Vérification et accès](#5-vérification)
5. [Mises à jour futures](#6-mises-à-jour)
6. [Commandes utiles](#7-commandes-utiles)
7. [Résolution de problèmes](#8-dépannage)

---

## 1. Prérequis Windows

### 1.1 Installer Docker Desktop

1. Télécharger **Docker Desktop pour Windows** :  
   👉 https://www.docker.com/products/docker-desktop/

2. Lancer l'installeur et accepter les conditions.

3. Au redémarrage, Docker Desktop démarre automatiquement.

4. Vérifier dans un terminal **PowerShell** :
   ```powershell
   docker --version
   docker compose version
   ```
   Résultat attendu :
   ```
   Docker version 24.x.x
   Docker Compose version v2.x.x
   ```

> ⚠️ Activer **WSL 2** si demandé par Docker Desktop. Suivre le lien affiché dans l'interface.

### 1.2 Activer WSL 2 (si pas encore fait)

Dans PowerShell **en tant qu'administrateur** :
```powershell
wsl --install
wsl --set-default-version 2
```
Redémarrer si demandé.

### 1.3 Se connecter à Docker Hub dans Docker Desktop

Ouvrir Docker Desktop → cliquer sur **Sign In** en haut à droite → entrer les identifiants `djtj21`.

Ou depuis PowerShell :
```powershell
docker login -u djtj21
# Entrer le mot de passe Docker Hub quand demandé
```

---

## 2. Préparer le dossier de déploiement

Sur la machine Windows, créer un dossier `C:\grequetes\` et y placer **uniquement 2 fichiers** :

### 2.1 Fichier `docker-compose.yml`

Créer `C:\grequetes\docker-compose.yml` avec ce contenu :

```yaml
version: '3.8'

networks:
  sygre_network:
    driver: bridge

volumes:
  mysql_data:
  redis_data:
  storage_data:

services:

  app:
    image: djtj21/g-requete-app:prod
    container_name: sygre_app
    restart: unless-stopped
    working_dir: /var/www/html
    volumes:
      - storage_data:/var/www/html/storage/app
    env_file:
      - .env
    environment:
      APP_ENV: production
      APP_DEBUG: "false"
      LOG_CHANNEL: stderr
    ports:
      - "8080:8080"
    depends_on:
      db:
        condition: service_healthy
      redis:
        condition: service_started
    networks:
      - sygre_network
    entrypoint: ["/var/www/html/docker/entrypoint.prod.sh"]
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost:8080/login"]
      interval: 30s
      timeout: 10s
      retries: 5
      start_period: 60s

  worker:
    image: djtj21/g-requete-app:prod
    container_name: sygre_worker
    restart: unless-stopped
    working_dir: /var/www/html
    volumes:
      - storage_data:/var/www/html/storage/app
    env_file:
      - .env
    environment:
      APP_ENV: production
      APP_DEBUG: "false"
      LOG_CHANNEL: stderr
    command: ["php", "artisan", "queue:work", "redis",
              "--sleep=3", "--tries=3", "--timeout=90"]
    depends_on:
      db:
        condition: service_healthy
      redis:
        condition: service_started
    networks:
      - sygre_network

  db:
    image: mysql:8.0
    container_name: sygre_db
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: ${DB_DATABASE:-sygre_db}
      MYSQL_USER: ${DB_USERNAME:-sygre_user}
      MYSQL_PASSWORD: ${DB_PASSWORD:-sygre_pass}
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD:-root_pass}
    volumes:
      - mysql_data:/var/lib/mysql
    networks:
      - sygre_network
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "127.0.0.1",
             "-u", "root", "-p${DB_ROOT_PASSWORD:-root_pass}"]
      interval: 10s
      timeout: 5s
      retries: 10
      start_period: 30s

  redis:
    image: redis:alpine
    container_name: sygre_redis
    restart: unless-stopped
    volumes:
      - redis_data:/data
    networks:
      - sygre_network
```

### 2.2 Fichier `.env`

Créer `C:\grequetes\.env` avec ce contenu (adapter les valeurs marquées ⚠️) :

```env
APP_NAME="G-REQUETES"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://localhost:8080

LOG_CHANNEL=stderr
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=sygre_db
DB_USERNAME=sygre_user
DB_PASSWORD=MotDePasseForte2025!        # ⚠️ Changer
DB_ROOT_PASSWORD=RootPassForte2025!     # ⚠️ Changer

CACHE_DRIVER=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120
QUEUE_CONNECTION=redis

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.votre-serveur.com        # ⚠️ Changer
MAIL_PORT=587
MAIL_USERNAME=noreply@iug.cm            # ⚠️ Changer
MAIL_PASSWORD=MotDePasseMail            # ⚠️ Changer
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@iug.cm
MAIL_FROM_NAME="G-REQUETES IUG"

FILESYSTEM_DISK=local
```

> **Note :** laisser `APP_KEY=` vide pour l'instant, elle sera générée à l'étape suivante.

---

## 3. Premier démarrage sur Windows

Ouvrir **PowerShell** dans le dossier `C:\grequetes\` :

```powershell
cd C:\grequetes
```

### 3.1 Tirer l'image depuis Docker Hub

```powershell
docker pull djtj21/g-requete-app:prod
```

> ⏳ Premier téléchargement : ~250 Mo. Les mises à jour suivantes ne téléchargent que les couches modifiées.

### 3.2 Démarrer les conteneurs

```powershell
docker compose up -d
```

Au premier démarrage, Docker télécharge aussi `mysql:8.0` et `redis:alpine` (~150 Mo).

### 3.3 Générer la clé d'application Laravel (1 seule fois)

```powershell
docker exec sygre_app php artisan key:generate --force
```

Copier la clé affichée (`base64:XXXX...`) dans `.env` à la ligne `APP_KEY=`, puis redémarrer :
```powershell
docker compose restart app worker
```

### 3.4 Vérifier le démarrage

```powershell
docker compose ps
```

Résultat attendu (tous `Up`) :
```
NAME            STATUS
sygre_app       Up (healthy)
sygre_db        Up (healthy)
sygre_redis     Up
sygre_worker    Up
```

> ⏳ L'app peut mettre **30 à 60 secondes** avant d'être disponible (migrations au démarrage).

---

## 5. Vérification et accès

### 5.1 Accéder à l'application

Ouvrir un navigateur et aller sur :
```
http://localhost:8080
```

### 5.2 Identifiants initiaux

| Rôle | Email | Mot de passe |
|---|---|---|
| Administrateur | `admin@iug.cm` | `Admin@1234` |
| Agent | `agent1@iug.cm` | `Agent@1234` |
| Étudiant | `arthur.kamdem@esg.cm` | `Etudiant@1234` |

> ⚠️ **Changer tous ces mots de passe après la première connexion.**

### 5.3 Accès réseau local (autres machines)

Pour accéder depuis d'autres PC du réseau, remplacer `localhost` par l'**adresse IP** de la machine Windows :

```powershell
# Trouver l'IP de la machine
ipconfig | findstr IPv4
```

Exemple : `http://192.168.1.50:8080`

Il peut être nécessaire d'autoriser le port 8080 dans le pare-feu Windows :
```powershell
# En tant qu'administrateur
netsh advfirewall firewall add rule name="G-REQUETES" dir=in action=allow protocol=TCP localport=8080
```

---

## 6. Mises à jour futures

Quand le code change sur la machine Linux de développement :

### Sur Linux (dev) — rebuilder et pousser
```bash
cd /home/romuald/G-REQUETE

# Reconstruire et retagger
docker build -t g-requete-app:prod -f docker/php/Dockerfile .
docker tag g-requete-app:prod djtj21/g-requete-app:prod

# Pousser sur Docker Hub
docker login -u djtj21
docker push djtj21/g-requete-app:prod
```

### Sur Windows (prod) — puller et redémarrer
```powershell
cd C:\grequetes

# Arrêter l'app (la DB continue de tourner)
docker compose stop app worker

# Tirer la nouvelle image depuis Docker Hub
docker compose pull app

# Redémarrer avec la nouvelle image
docker compose up -d app worker
```

Les migrations sont exécutées automatiquement au redémarrage.

---

## 7. Commandes utiles

```powershell
# Voir les logs de l'application
docker logs sygre_app -f

# Voir les logs du worker
docker logs sygre_worker -f

# Exécuter une commande Artisan
docker exec sygre_app php artisan <commande>

# Ouvrir un shell dans le conteneur
docker exec -it sygre_app bash

# Arrêter tous les conteneurs (données conservées)
docker compose stop

# Arrêter et SUPPRIMER les conteneurs (données conservées dans les volumes)
docker compose down

# Arrêter et SUPPRIMER les conteneurs ET les données (⚠️ irréversible)
docker compose down -v

# Backup de la base de données
docker exec sygre_db mysqldump -u sygre_user -psygre_pass sygre_db > backup_$(date +%Y%m%d).sql

# Restaurer un backup
type backup_20250410.sql | docker exec -i sygre_db mysql -u sygre_user -psygre_pass sygre_db
```

---

## 8. Dépannage

### ❌ "port is already allocated"
Un autre programme utilise le port 8080. Changer dans `docker-compose.yml` :
```yaml
ports:
  - "9090:8080"   # utiliser 9090 à la place
```

### ❌ L'app affiche une page blanche ou erreur 500
```powershell
docker logs sygre_app --tail=50
```
Vérifier que `APP_KEY` est bien renseigné dans `.env`.

### ❌ "No application encryption key has been specified"
```powershell
docker exec sygre_app php artisan key:generate --force
docker compose restart app worker
```

### ❌ MySQL ne démarre pas
```powershell
docker logs sygre_db --tail=30
```
Souvent causé par un mauvais mot de passe dans `.env`. Corriger et :
```powershell
docker compose down
docker volume rm deploiement-grequetes_mysql_data
docker compose up -d
```

### ❌ Docker Desktop ne démarre pas
- Vérifier que WSL 2 est activé : `wsl --status`
- Redémarrer Docker Desktop depuis le menu système
- En dernier recours : désinstaller/réinstaller Docker Desktop

---

*Guide généré le 10/04/2026 — G-REQUÊTES v1.0*
