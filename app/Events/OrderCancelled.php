<?php

namespace App\Events;

use App\Models\ServiceRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when an order is cancelled by either party.
 *
 * Broadcasts to:
 *   - The public orders channel (so providers stop showing the order)
 *   - The driver's private channel
 *   - The provider's private channel (if assigned)
 */
class OrderCancelled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ServiceRequest $order) {}

    public function broadcastOn(): array
    {
        $channels = [
            new Channel('orders.'.$this->order->service_type),
            new PrivateChannel('driver.'.$this->order->driver_id),
        ];

        if ($this->order->provider_id) {
            $channels[] = new PrivateChannel('provider.'.$this->order->provider_id);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'order.cancelled';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->order->id,
            'status' => 'cancelled',
            'cancelled_by' => $this->order->cancelled_by,
            'cancellation_reason' => $this->order->cancellation_reason,
            'cancelled_at' => $this->order->cancelled_at?->toISOString(),
        ];
    }
}
