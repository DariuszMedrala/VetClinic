#!/usr/bin/env bash
set -u

BASE_URL="${BASE_URL:-http://localhost:8080}"
COOKIE_JAR="$(mktemp)"
PASS=0
FAIL=0

cleanup() { rm -f "$COOKIE_JAR"; }
trap cleanup EXIT

code() {
    curl -s -o /dev/null -w '%{http_code}' "$@"
}

check() {
    local label="$1" expected="$2" actual="$3"
    if [ "$actual" = "$expected" ]; then
        printf '  \033[32mPASS\033[0m  %-46s [%s]\n' "$label" "$actual"
        PASS=$((PASS + 1))
    else
        printf '  \033[31mFAIL\033[0m  %-46s [oczekiwano %s, otrzymano %s]\n' "$label" "$expected" "$actual"
        FAIL=$((FAIL + 1))
    fi
}

echo "VetClinic — testy integracyjne endpointów (${BASE_URL})"
echo "-------------------------------------------------------------"

echo "Strony publiczne:"
check "GET /                      → 200" 200 "$(code "$BASE_URL/")"
check "GET /login                 → 200" 200 "$(code "$BASE_URL/login")"
check "GET /register              → 200" 200 "$(code "$BASE_URL/register")"
check "GET /terms                 → 200" 200 "$(code "$BASE_URL/terms")"
check "GET /reset-password        → 200" 200 "$(code "$BASE_URL/reset-password")"

echo "Obsługa błędów:"
check "GET /nie-ma-takiej-strony  → 404" 404 "$(code "$BASE_URL/nie-ma-takiej-strony")"
check "GET /totally/unknown/path  → 404" 404 "$(code "$BASE_URL/totally/unknown/path")"
check "DELETE /login (zła metoda) → 400" 400 "$(code -X DELETE "$BASE_URL/login")"
check "PUT /login (zła metoda)    → 400" 400 "$(code -X PUT "$BASE_URL/login")"

echo "Ochrona dostępu (bez logowania):"
check "GET /dashboard  → 302 /login" 302 "$(code "$BASE_URL/dashboard")"
check "GET /portal     → 302 /login" 302 "$(code "$BASE_URL/portal")"
check "GET /catalog    → 302 /login" 302 "$(code "$BASE_URL/catalog")"
check "GET /profile    → 302 /login" 302 "$(code "$BASE_URL/profile")"

echo "CSRF / walidacja żądań:"
check "POST /login bez CSRF       → 419" 419 "$(code -X POST "$BASE_URL/login")"
check "POST /register bez CSRF    → 419" 419 "$(code -X POST "$BASE_URL/register")"

echo "Zasoby statyczne:"
check "GET /assets/css/styles.css → 200" 200 "$(code "$BASE_URL/assets/css/styles.css")"
check "GET /assets/js/nav.js      → 200" 200 "$(code "$BASE_URL/assets/js/nav.js")"

echo "-------------------------------------------------------------"
printf 'Wynik: \033[32m%d zaliczonych\033[0m, \033[31m%d niezaliczonych\033[0m\n' "$PASS" "$FAIL"

[ "$FAIL" -eq 0 ]
