<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\ProviderProfile;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $serviceType = $this->resolveServiceType();

        $completed = ServiceRequest::where('status', 'completed')
            ->when($serviceType, fn ($q) => $q->where('service_type', $serviceType));

        $baseQuery = ServiceRequest::query()
            ->when($serviceType, fn ($q) => $q->where('service_type', $serviceType));

        // ── Status & Type Breakdown ──────────────────────────
        $byStatus = (clone $baseQuery)->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status');

        $byType = (clone $baseQuery)->select('service_type', DB::raw('count(*) as total'))
            ->groupBy('service_type')->pluck('total', 'service_type');

        // ── Revenue ─────────────────────────────────────────
        $revenue = [
            'total'         => (clone $completed)->sum('total'),
            'today'         => (clone $completed)->whereDate('completed_at', today())->sum('total'),
            'yesterday'     => (clone $completed)->whereDate('completed_at', today()->subDay())->sum('total'),
            'this_week'     => (clone $completed)->where('completed_at', '>=', now()->startOfWeek())->sum('total'),
            'this_month'    => (clone $completed)->whereMonth('completed_at', now()->month)->whereYear('completed_at', now()->year)->sum('total'),
            'fuel_revenue'  => (clone $completed)->where('service_type', 'fuel_delivery')->sum('total'),
            'tow_revenue'   => (clone $completed)->where('service_type', 'towing')->sum('total'),
            'service_fees'  => (clone $completed)->sum('service_fee'),
        ];

        // ── Completion Rate ─────────────────────────────────
        $totalRequests = $byStatus->sum();
        $completedCount = $byStatus->get('completed', 0);
        $cancelledCount = $byStatus->get('cancelled', 0);
        $completionRate = $totalRequests > 0 ? round(($completedCount / $totalRequests) * 100) : 0;
        $cancellationRate = $totalRequests > 0 ? round(($cancelledCount / $totalRequests) * 100) : 0;

        // ── Daily Trends (last 30 days) ─────────────────────
        $perDay = ServiceRequest::query()
            ->when($serviceType, fn ($q) => $q->where('service_type', $serviceType))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total'),
                DB::raw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed"),
                DB::raw("SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled"),
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')->orderBy('date')->get();

        // ── Revenue per Day (last 30 days) ──────────────────
        $revenuePerDay = ServiceRequest::where('status', 'completed')
            ->when($serviceType, fn ($q) => $q->where('service_type', $serviceType))
            ->select(
                DB::raw('DATE(completed_at) as date'),
                DB::raw('ROUND(SUM(total), 2) as revenue'),
            )
            ->where('completed_at', '>=', now()->subDays(30))
            ->groupBy('date')->orderBy('date')->get();

        // ── Peak Hours ──────────────────────────────────────
        $peakHours = ServiceRequest::query()
            ->when($serviceType, fn ($q) => $q->where('service_type', $serviceType))
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('count(*) as total'),
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('hour')->orderBy('hour')->pluck('total', 'hour');

        // ── Top Providers ───────────────────────────────────
        $topProviders = ProviderProfile::where('verification_status', 'approved')
            ->when($serviceType, fn ($q) => $q->where('service_type', $serviceType))
            ->with('user')
            ->withCount([
                'serviceRequests as completed_orders' => fn ($q) => $q->where('status', 'completed'),
            ])
            ->addSelect(['total_revenue' => ServiceRequest::select(DB::raw('COALESCE(SUM(total), 0)'))
                ->whereColumn('provider_id', 'provider_profiles.id')
                ->where('status', 'completed'),
            ])
            ->addSelect(['real_avg_rating' => DB::table('ratings')
                ->join('service_requests', 'service_requests.id', '=', 'ratings.request_id')
                ->where('ratings.rated_by', 'driver')
                ->whereColumn('service_requests.provider_id', 'provider_profiles.id')
                ->selectRaw('ROUND(AVG(ratings.rating), 1)'),
            ])
            ->addSelect(['real_total_ratings' => DB::table('ratings')
                ->join('service_requests', 'service_requests.id', '=', 'ratings.request_id')
                ->where('ratings.rated_by', 'driver')
                ->whereColumn('service_requests.provider_id', 'provider_profiles.id')
                ->selectRaw('COUNT(*)'),
            ])
            ->having('completed_orders', '>', 0)
            ->orderByDesc('completed_orders')
            ->take(10)->get();

        // ── Users by Role ───────────────────────────────────
        $usersByRole = User::select('role', DB::raw('count(*) as total'))
            ->groupBy('role')->pluck('total', 'role');

        // ── Average order value ─────────────────────────────
        $avgOrderValue = $completedCount > 0
            ? round($revenue['total'] / $completedCount, 2) : 0;

        // ── Current service fee ─────────────────────────────
        $currentServiceFee = AppSetting::getValue('service_fee', 15);

        return view('admin.analytics.index', compact(
            'byStatus',
            'byType',
            'perDay',
            'revenuePerDay',
            'peakHours',
            'topProviders',
            'usersByRole',
            'revenue',
            'completionRate',
            'cancellationRate',
            'totalRequests',
            'completedCount',
            'cancelledCount',
            'avgOrderValue',
            'currentServiceFee',
        ));
    }

    private function resolveServiceType(): ?string
    {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return null;
        }

        return match ($user->employee_type) {
            'fuel' => 'fuel_delivery',
            'towing' => 'towing',
            default => null,
        };
    }
}
