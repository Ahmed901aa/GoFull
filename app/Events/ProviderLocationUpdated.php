<?php

namespace App\Events;

use App\Models\ServiceRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Live provider GPS position for an active order.
 *
 * Broadcast on the driver's private channel so the customer's map
 * tracks the provider in real time over Reverb (WebSocket) instead
 * of the app polling the REST API.
 *
 * Uses ShouldBroadcastNow (skips the queue): location updates are
 * frequent and ephemeral — a queued backlog of stale positions is
 * worse than dropping straight through.
 */
class ProviderLocationUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ServiceRequest $order,
        public float $latitude,
        public float $longitude,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('driver.'.$this->order->driver_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'provider.location.updated';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'request_id' => $this->order->id,
            'provider_id' => $this->order->provider_id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'updated_at' => now()->toISOString(),
        ];
    }
}
