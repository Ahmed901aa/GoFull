#!/bin/bash
# Full API test: fix storage, login, fetch real data
set +e
BASE="http://localhost:8000"
OUT="api_test_output.txt"

echo "=== GoFull Full API Test ===" > "$OUT"
echo "Date: $(date)" >> "$OUT"
echo "" >> "$OUT"

echo "🔧 [1/5] Fixing storage symlink..."
{
  echo "── 1. Storage Symlink Fix ──"
  php artisan storage:link 2>&1
  echo ""
} >> "$OUT"

echo "🔐 [2/5] Logging in as DRIVER (0915909734 / 12345678)..."
DRIVER_RESPONSE=$(curl -s -X POST \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"phone":"0915909734","password":"12345678"}' \
  "$BASE/api/auth/login")
DRIVER_TOKEN=$(echo "$DRIVER_RESPONSE" | grep -oE '"token":"[^"]+"' | sed 's/"token":"//;s/"$//')
{
  echo "── 2. Driver Login ──"
  echo "$DRIVER_RESPONSE" | head -50
  echo ""
  echo "Token captured: ${DRIVER_TOKEN:0:30}..."
  echo ""
} >> "$OUT"

echo "🔐 [3/5] Logging in as PROVIDER (0923663333 / 12345678)..."
PROVIDER_RESPONSE=$(curl -s -X POST \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"phone":"0923663333","password":"12345678"}' \
  "$BASE/api/auth/login")
PROVIDER_TOKEN=$(echo "$PROVIDER_RESPONSE" | grep -oE '"token":"[^"]+"' | sed 's/"token":"//;s/"$//')
{
  echo "── 3. Provider Login ──"
  echo "$PROVIDER_RESPONSE" | head -50
  echo ""
  echo "Token captured: ${PROVIDER_TOKEN:0:30}..."
  echo ""
} >> "$OUT"

echo "📋 [4/5] Fetching driver's requests with token..."
{
  echo "── 4. Driver: GET /api/driver/requests ──"
  curl -s -w "\nHTTP: %{http_code}\n" \
    -H "Accept: application/json" \
    -H "Authorization: Bearer $DRIVER_TOKEN" \
    "$BASE/api/driver/requests" | head -80
  echo ""
  echo ""
} >> "$OUT"

echo "📊 [5/5] Fetching provider analytics with token..."
{
  echo "── 5. Provider: GET /api/provider/analytics/summary ──"
  curl -s -w "\nHTTP: %{http_code}\n" \
    -H "Accept: application/json" \
    -H "Authorization: Bearer $PROVIDER_TOKEN" \
    "$BASE/api/provider/analytics/summary" | head -100
  echo ""
} >> "$OUT"

echo ""
echo "✅ Done! Report: $OUT"
echo ""
echo "Run:  cat $OUT"
