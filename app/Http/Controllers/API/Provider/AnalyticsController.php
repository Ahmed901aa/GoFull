<?php

namespace App\Http\Controllers\API\Provider;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\ServiceRequest;
use Illuminate\Http\JsonResponse;

class AnalyticsController extends Controller
{
    public function summary(): JsonResponse
    {
        $provider = auth()->user()->providerProfile;

        if (! $provider) {
            return response()->json(['success' => false, 'message' => 'Provider profile not found.'], 404);
        }

        $completedQuery = ServiceRequest::where('provider_id', $provider->id)
            ->where('status', 'completed');

        $totalOrders = (clone $completedQuery)->count();
        $totalIncome = (clone $completedQuery)->sum('total');

        // Today stats
        $todayOrders = (clone $completedQuery)
            ->whereDate('completed_at', today())
            ->count();
        $todayIncome = (clone $completedQuery)
            ->whereDate('completed_at', today())
            ->sum('total');

        // Yesterday stats for comparison
        $yesterdayOrders = (clone $completedQuery)
            ->whereDate('completed_at', today()->subDay())
            ->count();
        $yesterdayIncome = (clone $completedQuery)
            ->whereDate('completed_at', today()->subDay())
            ->sum('total');

        // Average rating
        $ratingStats = Rating::where('rated_by', 'driver')->whereIn('request_id', function ($q) use ($provider) {
            $q->select('id')
                ->from('service_requests')
                ->where('provider_id', $provider->id)
                ->where('status', 'completed');
        })->selectRaw('ROUND(AVG(rating), 2) as avg_rating, COUNT(*) as total_ratings')
            ->first();

        $averageRating = (float) ($ratingStats->avg_rating ?? 0);
        $totalRatings = (int) ($ratingStats->total_ratings ?? 0);

        // Weekly orders breakdown (last 7 days) for bar chart
        $weeklyOrders = [];
        $dayLabels = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];

        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $count = ServiceRequest::where('provider_id', $provider->id)
                ->where('status', 'completed')
                ->whereDate('completed_at', $date)
                ->count();

            $weeklyOrders[] = [
                'day' => $dayLabels[$date->dayOfWeek],
                'date' => $date->toDateString(),
                'count' => $count,
            ];
        }

        // Weekly acceptance rate (last 7 days) for area chart
        $weeklyAcceptance = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);

            $accepted = ServiceRequest::where('provider_id', $provider->id)
                ->whereIn('status', ['accepted', 'en_route', 'arrived', 'in_progress', 'completed'])
                ->whereDate('accepted_at', $date)
                ->count();

            $total = ServiceRequest::where('provider_id', $provider->id)
                ->whereDate('created_at', $date)
                ->count();

            $weeklyAcceptance[] = [
                'day' => $dayLabels[$date->dayOfWeek],
                'date' => $date->toDateString(),
                'rate' => $total > 0 ? round(($accepted / $total) * 100) : 0,
            ];
        }

        // Percentage changes vs yesterday
        $ordersChange = $yesterdayOrders > 0
            ? round((($todayOrders - $yesterdayOrders) / $yesterdayOrders) * 100)
            : ($todayOrders > 0 ? 100 : 0);

        $incomeChange = $yesterdayIncome > 0
            ? round((($todayIncome - $yesterdayIncome) / $yesterdayIncome) * 100)
            : ($todayIncome > 0 ? 100 : 0);

        return response()->json([
            'success' => true,
            'data' => [
                'total_orders' => $totalOrders,
                'total_income' => round($totalIncome, 2),
                'today_orders' => $todayOrders,
                'today_income' => round($todayIncome, 2),
                'orders_change' => $ordersChange,
                'income_change' => $incomeChange,
                'average_rating' => $averageRating,
                'total_ratings' => $totalRatings,
                'weekly_orders' => $weeklyOrders,
                'weekly_acceptance' => $weeklyAcceptance,
            ],
        ]);
    }
}
