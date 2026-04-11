#!/bin/bash
set -e

KEY_FILE="/var/www/html/storage/app/.app_key"

echo "=== G-REQUETES :: Worker/Scheduler startup ==="

# ── Attendre que l'app génère l'APP_KEY ─────────────────
echo "[1/2] Waiting for APP_KEY from app container..."
TRIES=0
until [ -f "$KEY_FILE" ] && grep -q "^base64:" "$KEY_FILE" 2>/dev/null; do
    sleep 2
    TRIES=$((TRIES + 1))
    if [ "$TRIES" -ge 30 ]; then
        echo "  ERROR: APP_KEY non trouvé après 60s. Arrêt."
        exit 1
    fi
done

STORED_KEY=$(cat "$KEY_FILE")
export APP_KEY="$STORED_KEY"
echo "  APP_KEY chargé depuis le volume partagé."

# ── Lancer la commande passée en argument ───────────────
echo "[2/2] Starting: $*"
exec "$@"
