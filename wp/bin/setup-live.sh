#!/usr/bin/env bash
#
# Installerar WordPress och aktiverar den trogna noav.se-kopian (temat
# noav-live). Idempotent — kör om den när som helst.
#
#   cd wp && docker compose -f docker-compose.copy.yml up -d && ./bin/setup-live.sh
#
# Sajten: http://localhost:8081  ·  wp-admin: http://localhost:8081/wp-admin
# Inloggning: noav / noav
#
# När temat aktiveras skapar det självt de åtta sidorna (startsida + sju
# sektioner), sätter permalänkar och pekar ut startsidan — se
# after_switch_theme i themes/noav-live/functions.php.

set -euo pipefail
cd "$(dirname "$0")/.."

COMPOSE="docker compose -f docker-compose.copy.yml"
SITE_URL="http://localhost:8081"

wp() { $COMPOSE run --rm -T cli-live "$@"; }

echo "→ Väntar på att databasen ska svara …"
until wp db check >/dev/null 2>&1; do sleep 2; done

if ! wp core is-installed >/dev/null 2>&1; then
  echo "→ Installerar WordPress …"
  wp core install \
    --url="$SITE_URL" \
    --title="Vinkelvikens HVB" \
    --admin_user="noav" \
    --admin_password="noav" \
    --admin_email="admin@example.com" \
    --skip-email
else
  echo "→ WordPress redan installerat."
fi

echo "→ Sätter svenska …"
wp language core install sv_SE >/dev/null 2>&1 || true
wp site switch-language sv_SE >/dev/null 2>&1 || true

echo "→ Aktiverar temat noav-live (skapar sidor + startsida) …"
wp theme activate noav-live

echo "→ Skriver om permalänkar …"
wp rewrite structure '/%postname%/' >/dev/null 2>&1 || true
wp rewrite flush >/dev/null 2>&1 || true

echo ""
echo "✓ Klart. Öppna $SITE_URL"
echo "  wp-admin: $SITE_URL/wp-admin  (noav / noav)"
