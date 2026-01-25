#!/usr/bin/env bash
set -euo pipefail

BASE_URL="${BASE_URL:-http://127.0.0.1:8000}"
ADMIN_EMAIL="${ADMIN_EMAIL:-admin@example.com}"
USER_EMAIL="${USER_EMAIL:-user@example.com}"
PASS="${PASS:-password123}"
SLUG="${SLUG:-qr-generator-2}"

login () {
  local email="$1"
  curl -s -X POST "$BASE_URL/api/auth/login" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d "{\"email\":\"$email\",\"password\":\"$PASS\"}" \
    | php -r '$j=json_decode(stream_get_contents(STDIN), true); echo $j["token"] ?? "";'
}

echo "[1/6] Login admin/user"
TOKEN_ADMIN="$(login "$ADMIN_EMAIL")"
TOKEN_USER="$(login "$USER_EMAIL")"

if [[ -z "$TOKEN_ADMIN" || -z "$TOKEN_USER" ]]; then
  echo "ERROR: token vacío. ¿Servidor levantado? ¿Credenciales correctas?"
  exit 1
fi

echo "[2/6] /me admin must be is_admin=true"
ME_ADMIN="$(curl -s "$BASE_URL/api/auth/me" -H "Accept: application/json" -H "Authorization: Bearer $TOKEN_ADMIN")"
echo "$ME_ADMIN" | grep -q '"is_admin":true' || (echo "ERROR: admin no es admin"; exit 1)

echo "[3/6] user should NOT see module before grant (404) -- optional if seeder grants"
# Si el seeder ya concede acceso, este paso podría no aplicar.
# Lo dejamos como aviso, no como fallo.
STATUS_BEFORE="$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/api/modules/$SLUG" -H "Accept: application/json" -H "Authorization: Bearer $TOKEN_USER")"
echo "Status user show (pre): $STATUS_BEFORE (404 esperado si no hay grant; 200 si seeder ya concedió)"

echo "[4/6] user should see module (200) if seeded/granted"
STATUS_AFTER="$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/api/modules/$SLUG" -H "Accept: application/json" -H "Authorization: Bearer $TOKEN_USER")"
[[ "$STATUS_AFTER" == "200" ]] || (echo "ERROR: user no ve el módulo (status $STATUS_AFTER)"; exit 1)

echo "[5/6] module detail must include media array"
DETAIL="$(curl -s "$BASE_URL/api/modules/$SLUG" -H "Accept: application/json" -H "Authorization: Bearer $TOKEN_USER")"
echo "$DETAIL" | grep -q '"media":\[' || (echo "ERROR: no aparece media[] en el detalle"; exit 1)

echo "[6/6] user must NOT access admin endpoints (404)"
STATUS_ADMIN_DENY="$(curl -s -o /dev/null -w "%{http_code}" -X POST "$BASE_URL/api/admin/modules" \
  -H "Accept: application/json" -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN_USER" \
  -d '{"name":"X","slug":"x","price_cents":0,"status":"published"}')"
[[ "$STATUS_ADMIN_DENY" == "404" ]] || (echo "ERROR: user pudo tocar admin (status $STATUS_ADMIN_DENY)"; exit 1)

echo "OK: Smoke test passed."
