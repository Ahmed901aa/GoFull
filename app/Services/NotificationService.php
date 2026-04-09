<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;

class NotificationService
{
    public static function send(User $user, string $title, string $body, array $data = []): void
    {
        // Always persist the notification row even if push fails
        try {
            Notification::create([
                'user_id' => $user->id,
                'title'   => $title,
                'body'    => $body,
                'data'    => $data,
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Failed to persist notification', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
        }

        // FCM push is best-effort; swallow any errors so callers keep running
        if (! $user->fcm_token) {
            return;
        }

        $serverKey = config('services.fcm.server_key');
        if (empty($serverKey)) {
            return;
        }

        try {
            Http::timeout(3)->withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type'  => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', [
                'to'           => $user->fcm_token,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                    'sound' => 'default',
                ],
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            \Log::warning('FCM push failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    public static function sendToMany(iterable $users, string $title, string $body, array $data = []): void
    {
        foreach ($users as $user) {
            try {
                static::send($user, $title, $body, $data);
            } catch (\Throwable $e) {
                \Log::warning('sendToMany iteration failed', [
                    'user_id' => $user->id ?? null,
                    'error'   => $e->getMessage(),
                ]);
            }
        }
    }
}