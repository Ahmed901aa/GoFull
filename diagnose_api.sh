#!/bin/bash
# GoFull API Diagnostic Script
# Run from inside GoFull directory: bash diagnose_api.sh

set +e
BASE="http://localhost:8000"
OUT="diagnose_output.txt"

# Reset output file
echo "=== GoFull API Diagnostic Report ===" > "$OUT"
echo "Date: $(date)" >> "$OUT"
echo "" >> "$OUT"

echo "🔍 Running diagnostics... (output saves to $OUT)"

# ───── 1. PHP & Laravel ─────
echo "[1/8] Checking PHP & Laravel..."
{
  echo "── 1. PHP & Laravel ──"
  php --version | head -1
  php artisan --version 2>&1
  echo ""
} >> "$OUT"

# ───── 2. .env settings ─────
echo "[2/8] Checking .env..."
{
  echo "── 2. Environment ──"
  grep -E "^(APP_ENV|APP_URL|APP_DEBUG|DB_)" .env 2>/dev/null | sed 's/DB_PASSWORD=.*/DB_PASSWORD=***hidden***/'
  echo ""
} >> "$OUT"

# ───── 3. Storage symlink ─────
echo "[3/8] Checking storage symlink..."
{
  echo "── 3. Storage Symlink ──"
  if [ -L "public/storage" ]; then
    echo "✅ Symlink exists: $(readlink public/storage)"
  else
    echo "❌ MISSING — run: php artisan storage:link"
  fi
  echo ""
} >> "$OUT"

# ───── 4. DB connection & row counts ─────
echo "[4/8] Checking database..."
{
  echo "── 4. Database ──"
  php artisan tinker --execute="
    try {
      \$users = \App\Models\User::count();
      \$sr    = \App\Models\ServiceRequest::count();
      \$pp    = \App\Models\ProviderProfile::count();
      \$rat   = \App\Models\Rating::count();
      \$ban   = \App\Models\Banner::count();
      \$fp    = \App\Models\FuelPrice::count();
      echo \"✅ DB connection OK\n\";
      echo \"users: \$users\n\";
      echo \"service_requests: \$sr\n\";
      echo \"provider_profiles: \$pp\n\";
      echo \"ratings: \$rat\n\";
      echo \"banners: \$ban\n\";
      echo \"fuel_prices: \$fp\n\";
    } catch (\Throwable \$e) {
      echo \"❌ DB ERROR: \" . \$e->getMessage() . \"\n\";
    }
  " 2>&1
  echo ""
} >> "$OUT"

# ───── 5. Server reachable ─────
echo "[5/8] Checking server..."
{
  echo "── 5. Server Reachability ──"
  CODE=$(curl -s -o /dev/null -w "%{http_code}" --max-time 5 "$BASE")
  if [ "$CODE" = "000" ]; then
    echo "❌ Server NOT reachable at $BASE (is 'php artisan serve' running?)"
  else
    echo "✅ Server responding with HTTP $CODE"
  fi
  echo ""
} >> "$OUT"

# ───── 6. Public endpoints (no auth) ─────
echo "[6/8] Testing public endpoints..."
{
  echo "── 6. Public Endpoints ──"
  for path in "/api/fuel/prices" "/api/app/settings"; do
    echo "GET $path"
    curl -s -w "\nHTTP: %{http_code}\n" -H "Accept: application/json" --max-time 5 "$BASE$path" | head -50
    echo "---"
  done
  echo ""
} >> "$OUT"

# ───── 7. Login attempt ─────
echo "[7/8] Trying login..."
{
  echo "── 7. Login Endpoint ──"
  echo "Sample request bodies tried:"
  for body in \
    '{"phone":"0912345678","password":"password"}' \
    '{"email":"admin@gofull.ly","password":"password"}' \
    '{"phone":"0913456789","password":"123456"}'; do
    echo ""
    echo "POST /api/auth/login  body: $body"
    curl -s -w "\nHTTP: %{http_code}\n" \
      -X POST \
      -H "Accept: application/json" \
      -H "Content-Type: application/json" \
      -d "$body" \
      --max-time 5 \
      "$BASE/api/auth/login" | head -30
    echo "---"
  done
} >> "$OUT"

# ───── 8. Sample users in DB ─────
echo "[8/8] Pulling sample users..."
{
  echo ""
  echo "── 8. Sample Users (first 5) ──"
  php artisan tinker --execute="
    foreach(\App\Models\User::take(5)->get() as \$u) {
      echo sprintf(\"id=%d  role=%s  phone=%s  email=%s\n\",
        \$u->id, \$u->role ?? '-', \$u->phone ?? '-', \$u->email ?? '-');
    }
  " 2>&1
} >> "$OUT"

echo ""
echo "✅ Done! Report saved to: $OUT"
echo ""
echo "Now share this report with me — open it with:"
echo "  cat $OUT"
echo "  # OR open in TextEdit:"
echo "  open -a TextEdit $OUT"
