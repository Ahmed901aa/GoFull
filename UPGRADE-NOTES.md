# GoFull Backend Upgrade — 2026-08-06

Four improvements: atomic dispatch, request timeout, radius filtering, per-provider rejection, OTP phone verification, and realtime provider location over Reverb.

## Deploy steps

```bash
php artisan migrate          # 3 new migrations
php artisan schedule:work    # dev — production needs the cron below
```

Production cron (Railway: add as a separate service or use `schedule:work` in start.sh):

```
* * * * * php artisan schedule:run >> /dev/null 2>&1
```

Required `.env` (already referenced by config/services.php):

```
ISEND_API_TOKEN=...
ISEND_SENDER_ID=GoFull
```

## What changed

### 1. Dispatch logic
- `accept()` — atomic conditional UPDATE inside a transaction. Two providers tapping "accept" simultaneously: exactly one wins, the other gets 422.
- `reject()` — **behavior change**: rejection is now per-provider (`request_rejections` table). The request stays `pending` for other providers instead of being cancelled for everyone. The driver is no longer notified on reject.
- `index()` — pending requests filtered to within **30 km** of the provider (Haversine, MySQL only), ordered nearest-first. Falls back to newest-first if the provider has no stored location.
- `orders:expire` command (scheduled every minute) — cancels pending requests older than the timeout with `cancelled_by = system`. Timeout is admin-configurable via `app_settings` key `request_timeout_minutes` (default 15).

### 2. OTP phone verification
- New: `otps` table, `Otp` model, `OtpController`, `users.phone_verified_at`.
- `OtpService.verify()` hardened: max 5 wrong attempts, constant-time compare.
- Routes:
  - `POST /api/auth/otp/send` `{phone, purpose}` — throttle 3/min
  - `POST /api/auth/otp/verify` `{phone, code, purpose}` — throttle 10/min (for password-reset flows)
- **Breaking**: `POST /api/auth/register` now requires `otp_code` (6 digits). Login/register are throttled 10/min.

### 3. Realtime location (Reverb)
- `PATCH /api/provider/profile/location` now:
  - Skips writes when moved < 15 m within 30 s (GPS jitter).
  - Broadcasts `provider.location.updated` on private channel `driver.{driver_id}` when the provider has an active order (`ShouldBroadcastNow` — no queue lag).
- Payload: `{request_id, provider_id, latitude, longitude, updated_at}`.

## Flutter integration

**Registration flow**: phone entry → `POST /auth/otp/send` → code entry screen → include `otp_code` in the existing register call. Handle 422 with the Arabic message from the API directly.

**Provider location sending** (keep the REST PATCH, but throttle client-side):

```dart
// with geolocator: only send when moved ≥ 20 m
Geolocator.getPositionStream(
  locationSettings: const LocationSettings(
    accuracy: LocationAccuracy.high,
    distanceFilter: 20, // metres — replaces the fixed 10 s timer
  ),
).listen((pos) => api.updateLocation(pos.latitude, pos.longitude));
```

**Driver live map** (subscribe with laravel_echo + pusher_channels_flutter pointed at Reverb):

```dart
echo.private('driver.$userId')
  .listen('.provider.location.updated', (e) {
    // e: {request_id, latitude, longitude, ...}
    moveProviderMarker(e['latitude'], e['longitude']);
  });
```

Existing `.order.status.updated` and cancellation events are unchanged.

## New/changed files

- `app/Console/Commands/ExpireStaleRequests.php` (new)
- `app/Events/ProviderLocationUpdated.php` (new)
- `app/Http/Controllers/API/OtpController.php` (new)
- `app/Models/Otp.php`, `app/Models/RequestRejection.php` (new)
- `database/migrations/2026_08_06_*` — 3 new migrations
- Modified: `Provider/RequestController`, `Provider/ProfileController`, `AuthController`, `RegisterRequest`, `OtpService`, `User`, `ServiceRequest`, `ProviderProfile`, `routes/api.php`, `routes/console.php`

## Postman

Collection **"GoFull API"** was created in your Personal Workspace — all endpoints incl. the new OTP ones, with `{{base_url}}`/`{{token}}` variables. Login auto-saves the token.
