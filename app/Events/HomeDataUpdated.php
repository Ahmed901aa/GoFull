<?php

namespace App\Events;

use App\Models\AppSetting;
use App\Models\FuelPrice;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired whenever the admin changes home-page data (fuel prices or the
 * open-stations counter). Broadcasts the FULL fresh snapshot on a public
 * channel so every connected customer app replaces its local state —
 * idempotent, no merge logic needed client-side.
 *
 * ShouldBroadcastNow → bypasses the queue so the update is instant even
 * when no queue worker is running.
 *
 * Channel:  home-data  (public — no auth required)
 * Event:    home.data.updated
 */
class HomeDataUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function broadcastOn(): array
    {
        return [new Channel('home-data')];
    }

    public function broadcastAs(): string
    {
        return 'home.data.updated';
    }

    /**
     * Payload mirrors GET /api/fuel/prices and GET /api/app/settings so the
     * Flutter client parses it with the same models.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $prices = FuelPrice::active()
            ->get(['id', 'fuel_type', 'name_ar', 'price_per_liter', 'tax_type', 'tax_value'])
            ->map(fn (FuelPrice $price) => [
                'id'              => $price->id,
                'fuel_type'       => $price->fuel_type,
                'name_ar'         => $price->name_ar,
                'price_per_liter' => $price->price_per_liter,
                'tax_type'        => $price->tax_type,
                'tax_value'       => $price->tax_value,
                'tax_amount'      => $price->calculateTax(),
                'price_with_tax'  => $price->price_with_tax,
            ]);

        return [
            'fuel_prices' => $prices,
            'settings'    => AppSetting::all()->pluck('value', 'key'),
        ];
    }
}
