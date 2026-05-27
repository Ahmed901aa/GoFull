<?php

namespace Database\Seeders;

use App\Models\ProviderProfile;
use App\Models\Rating;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestOrdersAndRatingsSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::where('phone', '0911111111')->first();

        if (! $customer) {
            $this->command->warn('Test customer not found. Run DatabaseSeeder first.');

            return;
        }

        // ── Fuel provider (Ibrahim) ──
        $fuelProvider = ProviderProfile::whereHas('user', fn ($q) => $q->where('phone', '0923663333'))->first();

        if ($fuelProvider) {
            $this->createOrdersAndRatings($customer, $fuelProvider, 'fuel_delivery', [
                ['rating' => 5, 'comment' => 'خدمة ممتازة وسريعة', 'total' => 45.00, 'days_ago' => 30],
                ['rating' => 4, 'comment' => 'جيد جداً', 'total' => 60.00, 'days_ago' => 25],
                ['rating' => 5, 'comment' => 'أفضل خدمة وقود', 'total' => 37.50, 'days_ago' => 20],
                ['rating' => 4, 'comment' => 'سريع ومحترف', 'total' => 52.50, 'days_ago' => 15],
                ['rating' => 5, 'comment' => 'ممتاز', 'total' => 75.00, 'days_ago' => 10],
                ['rating' => 3, 'comment' => 'جيد', 'total' => 30.00, 'days_ago' => 7],
                ['rating' => 5, 'comment' => 'خدمة رائعة', 'total' => 48.75, 'days_ago' => 3],
                ['rating' => 4, 'comment' => 'شكراً', 'total' => 67.50, 'days_ago' => 1],
            ]);
        }

        // ── Towing provider (Muftah) ──
        $towProvider = ProviderProfile::whereHas('user', fn ($q) => $q->where('phone', '0923664444'))->first();

        if ($towProvider) {
            $this->createOrdersAndRatings($customer, $towProvider, 'towing', [
                ['rating' => 5, 'comment' => 'وصل بسرعة وسحب السيارة بأمان', 'total' => 65.00, 'days_ago' => 28],
                ['rating' => 4, 'comment' => 'خدمة جيدة', 'total' => 65.00, 'days_ago' => 21],
                ['rating' => 5, 'comment' => 'محترف جداً', 'total' => 65.00, 'days_ago' => 14],
                ['rating' => 5, 'comment' => 'أنصح بهذا السائق', 'total' => 65.00, 'days_ago' => 5],
                ['rating' => 4, 'comment' => 'جيد جداً شكراً', 'total' => 65.00, 'days_ago' => 2],
            ]);
        }

        $this->command->info('Test orders and ratings created successfully.');
    }

    /**
     * @param  array<int, array{rating: int, comment: string, total: float, days_ago: int}>  $items
     */
    private function createOrdersAndRatings(User $customer, ProviderProfile $provider, string $serviceType, array $items): void
    {
        $serviceFee = 15.00;

        foreach ($items as $item) {
            $completedAt = now()->subDays($item['days_ago']);
            $subtotal = $item['total'] - $serviceFee;

            $requestData = [
                'driver_id' => $customer->id,
                'provider_id' => $provider->id,
                'service_type' => $serviceType,
                'status' => 'completed',
                'driver_latitude' => 32.8872 + (mt_rand(-100, 100) / 10000),
                'driver_longitude' => 13.1913 + (mt_rand(-100, 100) / 10000),
                'driver_address' => 'طرابلس، ليبيا',
                'payment_method' => 'cash',
                'payment_status' => 'paid',
                'subtotal' => round($subtotal, 2),
                'service_fee' => $serviceFee,
                'total' => $item['total'],
                'accepted_at' => $completedAt->copy()->subMinutes(20),
                'arrived_at' => $completedAt->copy()->subMinutes(10),
                'completed_at' => $completedAt,
                'created_at' => $completedAt->copy()->subMinutes(25),
                'updated_at' => $completedAt,
            ];

            if ($serviceType === 'fuel_delivery') {
                $requestData['fuel_type'] = 'petrol';
                $requestData['fuel_quantity'] = round($subtotal / 0.75, 2);
                $requestData['price_per_liter'] = 0.75;
            } else {
                $requestData['plate_number'] = 'ط م '.mt_rand(1000, 9999);
                $requestData['car_type'] = 'سيدان';
            }

            $request = ServiceRequest::create($requestData);

            Rating::updateOrCreate(
                ['request_id' => $request->id],
                [
                    'rating' => $item['rating'],
                    'comment' => $item['comment'],
                ]
            );
        }

        // Update stored averages on the provider profile (raw query to avoid GROUP BY issue)
        $ratingStats = DB::table('ratings')
            ->join('service_requests', 'service_requests.id', '=', 'ratings.request_id')
            ->where('service_requests.provider_id', $provider->id)
            ->selectRaw('ROUND(AVG(ratings.rating), 2) as avg_rating, COUNT(*) as total_count')
            ->first();

        $provider->update([
            'average_rating' => $ratingStats->avg_rating ?? 0,
            'total_ratings' => $ratingStats->total_count ?? 0,
        ]);
    }
}
