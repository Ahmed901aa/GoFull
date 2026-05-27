<?php

namespace App\Events;

use App\Models\ServiceRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired whenever an order's status changes (accepted, en_route,
 * arrived, in_progress, completed, cancelled).
 *
 * Broadcasts on two private channels:
 *   - provider.{provider_id}  → so the provider's app updates
 *   - driver.{driver_id}      → so the customer's app updates
 */
class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ServiceRequest $order) {}

    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('driver.'.$this->order->driver_id),
        ];

        if ($this->order->provider_id) {
            $channels[] = new PrivateChannel('provider.'.$this->order->provider_id);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'order.status.updated';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $this->order->load(['driver', 'provider.user']);

        return [
            'id' => $this->order->id,
            'status' => $this->order->status,
            'service_type' => $this->order->service_type,
            'provider_id' => $this->order->provider_id,
            'provider_name' => $this->order->provider?->user?->name,
            'driver_id' => $this->order->driver_id,
            'driver_name' => $this->order->driver?->name,
            'driver_latitude' => $this->order->driver_latitude,
            'driver_longitude' => $this->order->driver_longitude,
            'driver_address' => $this->order->driver_address,
            'total' => $this->order->total,
            'accepted_at' => $this->order->accepted_at?->toISOString(),
            'arrived_at' => $this->order->arrived_at?->toISOString(),
            'completed_at' => $this->order->completed_at?->toISOString(),
            'cancelled_at' => $this->order->cancelled_at?->toISOString(),
        ];
    }
}
