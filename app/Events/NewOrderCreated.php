<?php

namespace App\Events;

use App\Models\ServiceRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a customer creates a new service request.
 *
 * Broadcasts to a public channel scoped by service type so every
 * available provider of that type receives it instantly.
 */
class NewOrderCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ServiceRequest $order) {}

    /**
     * Private channel — the payload carries customer PII (name, phone,
     * exact GPS), so only authenticated, approved providers of this
     * service type may listen (see routes/channels.php).
     * Clients subscribe to "private-orders.fuel_delivery" / "private-orders.towing".
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('orders.'.$this->order->service_type),
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.created';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $this->order->load('driver');

        return [
            'id' => $this->order->id,
            'service_type' => $this->order->service_type,
            'is_emergency' => (bool) $this->order->is_emergency,
            'status' => $this->order->status,
            'driver_name' => $this->order->driver?->name,
            'driver_phone' => $this->order->driver?->phone,
            'driver_latitude' => $this->order->driver_latitude,
            'driver_longitude' => $this->order->driver_longitude,
            'driver_address' => $this->order->driver_address,
            'fuel_type' => $this->order->fuel_type,
            'fuel_quantity' => $this->order->fuel_quantity,
            'plate_number' => $this->order->plate_number,
            'car_type' => $this->order->car_type,
            'total' => $this->order->total,
            'notes' => $this->order->notes,
            'created_at' => $this->order->created_at?->toISOString(),
        ];
    }
}
