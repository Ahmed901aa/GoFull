<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Persist a notification row for the user.
     * Push delivery is not enabled — notifications are stored in the DB
     * and shown when the user opens the app's notifications screen.
     */
    public static function send(User $user, string $title, string $body, array $data = []): void
    {
        try {
            Notification::create([
                'user_id' => $user->id,
                'title'   => $title,
                'body'    => $body,
                'data'    => $data,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to persist notification', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    public static function sendToMany(iterable $users, string $title, string $body, array $data = []): void
    {
        foreach ($users as $user) {
            try {
                self::send($user, $title, $body, $data);
            } catch (\Throwable $e) {
                Log::warning('sendToMany iteration failed', [
                    'user_id' => $user->id ?? null,
                    'error'   => $e->getMessage(),
                ]);
            }
        }
    }
}
