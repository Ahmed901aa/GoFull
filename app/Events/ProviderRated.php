<?php

namespace App\Events;

use App\Models\ProviderProfile;
use App\Models\Rating;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a customer rates a provider after a completed order.
 *
 * Broadcasts the new rating and the provider's updated average
 * to the provider's private channel so the app updates in real time.
 */
class ProviderRated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ProviderProfile $provider,
        public Rating $rating,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('provider.'.$this->provider->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'provider.rated';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'provider_id' => $this->provider->id,
            'new_rating' => $this->rating->rating,
            'comment' => $this->rating->comment,
            'average_rating' => (float) $this->provider->average_rating,
            'total_ratings' => (int) $this->provider->total_ratings,
            'completed_orders' => $this->provider->serviceRequests()
                ->where('status', 'completed')->count(),
            'request_id' => $this->rating->request_id,
        ];
    }
}
