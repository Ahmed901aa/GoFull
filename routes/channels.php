<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Private channels require authentication. The callback receives the
| currently authenticated user and any wildcard parameters from the
| channel name. Return true to authorize, false to deny.
|
*/

// Driver (customer) channel — only the driver themselves can listen
Broadcast::channel('driver.{driverId}', function (User $user, int $driverId) {
    return $user->id === $driverId;
});

// Provider channel — only the provider who owns this profile can listen
Broadcast::channel('provider.{providerId}', function (User $user, int $providerId) {
    return $user->providerProfile?->id === $providerId;
});

// New/cancelled order feed per service type — carries customer PII, so it
// is restricted to authenticated, APPROVED providers of that exact type.
Broadcast::channel('orders.{serviceType}', function (User $user, string $serviceType) {
    return $user->isProvider()
        && $user->providerProfile?->isApproved()
        && $user->providerProfile->service_type === $serviceType;
});
