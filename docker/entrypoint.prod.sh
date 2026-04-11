#!/bin/bash
set -e

echo "======================================================"
echo "  G-REQUETES :: Production startup"
echo "======================================================"

# ── [1] Permissions ────────────────────────────────────────
echo "[1/7] Fixing storage permissions..."
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# ── [2] APP_KEY auto-génération si absent ou invalide ─────
echo "[2/7] Checking APP_KEY..."
KEY_FILE="/var/www/html/storage/app/.app_key"

case "$APP_KEY" in
    base64:????????????????????????????????????????????????*)
        echo "  APP_KEY valide fourni dans .env."
        echo "$APP_KEY" > "$KEY_FILE"
        ;;
    *)
        if [ -f "$KEY_FILE" ] && grep -q "^base64:" "$KEY_FILE" 2>/dev/null; then
            STORED_KEY=$(cat "$KEY_FILE")
            export APP_KEY="$STORED_KEY"
            echo "  APP_KEY restauré depuis le volume persistant."
        else
            echo "  APP_KEY absent — génération automatique..."
            GENERATED=$(php -r "echo 'base64:'.base64_encode(random_bytes(32));")
            export APP_KEY="$GENERATED"
            echo "$GENERATED" > "$KEY_FILE"
            echo "  APP_KEY généré et sauvegardé."
        fi
        ;;
esac

# ── [3] Attente MySQL ─────────────────────────────────────
echo "[3/7] Waiting for MySQL..."
until mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" --skip-ssl -e "SELECT 1" > /dev/null 2>&1; do
    sleep 2
    echo "  MySQL not ready yet, retrying..."
done
echo "  MySQL ready!"

# ── [4] Migrations ────────────────────────────────────────
echo "[4/7] Running migrations..."
php artisan migrate --force

# ── [5] Seed si DB vide ───────────────────────────────────
echo "[5/7] Seeding initial data..."
USER_COUNT=$(mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" --skip-ssl -se "SELECT COUNT(*) FROM utilisateurs;" 2>/dev/null || echo "0")
if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ]; then
    echo "  Base vide — seeding en cours..."
    php artisan db:seed --force
    echo "  Seed terminé. Comptes de test créés."
    echo "  > Admin  : admin@iug.cm      / Admin@1234"
    echo "  > Agent  : agent1@iug.cm     / Agent@1234"
    echo "  > Etud.  : arthur.kamdem@... / Etudiant@1234"
else
    echo "  Base déjà peuplée (${USER_COUNT} utilisateurs). Seed ignoré."
fi

# ── [6] Storage symlink ───────────────────────────────────
echo "[6/7] Creating storage symlink..."
php artisan storage:link || true

# ── [7] Cache optimisé ────────────────────────────────────
echo "[7/7] Caching config, routes and views..."
php artisan package:discover --ansi || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "======================================================"
echo "  Démarrage du serveur sur :8080"
echo "======================================================"
exec php artisan serve --host=0.0.0.0 --port=8080
