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
