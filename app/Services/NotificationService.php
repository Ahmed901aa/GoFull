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
        Notification::create([
            'user_id' => $user->id,
            'title'   => $title,
            'body'    => $body,
            'data'    => $data,
        ]);

        if ($user->fcm_token) {
            Http::withHeaders([
                'Authorization' => 'key=' . config('services.fcm.server_key'),
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
        }
    }

    public static function sendToMany(Collection $users, string $title, string $body, array $data = []): void
    {
        foreach ($users as $user) {
            static::send($user, $title, $body, $data);
        }
    }
}