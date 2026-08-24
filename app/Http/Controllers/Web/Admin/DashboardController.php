<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProviderProfile;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $serviceTypeFilter = $this->resolveServiceType($user);

        $requestsQuery = ServiceRequest::query()
            ->when($serviceTypeFilter, fn ($q) => $q->where('service_type', $serviceTypeFilter));

        $providersQuery = ProviderProfile::query()
            ->when($serviceTypeFilter, fn ($q) => $q->where('service_type', $serviceTypeFilter));

        // ── Core Stats ──────────────────────────────────────
        $stats = [
            'total_drivers' => User::where('role', 'driver')->count(),
            'total_providers' => (clone $providersQuery)->where('verification_status', 'approved')->count(),
            'pending_providers' => (clone $providersQuery)->where('verification_status', 'pending')->count(),
            'active_requests' => (clone $requestsQuery)->whereIn('status', [
                'pending', 'accepted', 'en_route', 'arrived', 'in_progress',
            ])->count(),
            'completed_today' => (clone $requestsQuery)->where('status', 'completed')
                ->whereDate('completed_at', today())->count(),
            'total_requests' => (clone $requestsQuery)->count(),
            'total_completed' => (clone $requestsQuery)->where('status', 'completed')->count(),
            'total_cancelled' => (clone $requestsQuery)->where('status', 'cancelled')->count(),
            'revenue_today' => (clone $requestsQuery)->where('status', 'completed')
                ->whereDate('completed_at', today())->sum('total'),
            'revenue_week' => (clone $requestsQuery)->where('status', 'completed')
                ->where('completed_at', '>=', now()->startOfWeek())->sum('total'),
            'revenue_month' => (clone $requestsQuery)->where('status', 'completed')
                ->where('completed_at', '>=', now()->startOfMonth())->sum('total'),
            'revenue_total' => (clone $requestsQuery)->where('status', 'completed')->sum('total'),
            'service_fees_total' => (clone $requestsQuery)->where('status', 'completed')->sum('service_fee'),
        ];

        // ── Fuel vs Towing breakdown ────────────────────────
        $fuelStats = [
            'orders' => (clone $requestsQuery)->where('service_type', 'fuel_delivery')->count(),
            'completed' => (clone $requestsQuery)->where('service_type', 'fuel_delivery')->where('status', 'completed')->count(),
            'revenue' => (clone $requestsQuery)->where('service_type', 'fuel_delivery')->where('status', 'completed')->sum('total'),
        ];
        $towingStats = [
            'orders' => (clone $requestsQuery)->where('service_type', 'towing')->count(),
            'completed' => (clone $requestsQuery)->where('service_type', 'towing')->where('status', 'completed')->count(),
            'revenue' => (clone $requestsQuery)->where('service_type', 'towing')->where('status', 'completed')->sum('total'),
        ];

        // ── Completion / Cancellation rate ──────────────────
        $totalForRate = $stats['total_completed'] + $stats['total_cancelled'];
        $completionRate = $totalForRate > 0 ? round(($stats['total_completed'] / $totalForRate) * 100, 1) : 0;
        $cancellationRate = $totalForRate > 0 ? round(($stats['total_cancelled'] / $totalForRate) * 100, 1) : 0;

        // ── Revenue per day (last 14 days) ──────────────────
        $revenuePerDay = ServiceRequest::where('status', 'completed')
            ->when($serviceTypeFilter, fn ($q) => $q->where('service_type', $serviceTypeFilter))
            ->where('completed_at', '>=', now()->subDays(13)->startOfDay())
            ->select(
                DB::raw('DATE(completed_at) as date'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total) as revenue'),
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill missing days
        $chartDays = collect();
        for ($i = 13; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $found = $revenuePerDay->firstWhere('date', $d);
            $chartDays->push([
                'date' => Carbon::parse($d)->format('m/d'),
                'orders' => $found->orders ?? 0,
                'revenue' => (float) ($found->revenue ?? 0),
            ]);
        }

        // ── Orders by status ────────────────────────────────
        $byStatus = ServiceRequest::query()
            ->when($serviceTypeFilter, fn ($q) => $q->where('service_type', $serviceTypeFilter))
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // ── Peak hours (last 30 days) ───────────────────────
        $peakHours = ServiceRequest::query()
            ->when($serviceTypeFilter, fn ($q) => $q->where('service_type', $serviceTypeFilter))
            ->where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy('hour')
            ->pluck('count', 'hour')
            ->toArray();

        // ── Recent Requests ─────────────────────────────────
        $recentRequests = ServiceRequest::with(['driver', 'provider.user'])
            ->when($serviceTypeFilter, fn ($q) => $q->where('service_type', $serviceTypeFilter))
            ->latest()
            ->take(8)
            ->get();

        // ── Top Providers ───────────────────────────────────
        $topProviders = ProviderProfile::where('verification_status', 'approved')
            ->when($serviceTypeFilter, fn ($q) => $q->where('service_type', $serviceTypeFilter))
            ->with('user')
            ->withCount([
                'serviceRequests as completed_orders' => fn ($q) => $q->where('status', 'completed'),
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
            ->addSelect(['total_revenue' => ServiceRequest::select(DB::raw('COALESCE(SUM(total), 0)'))
                ->whereColumn('provider_id', 'provider_profiles.id')
                ->where('status', 'completed'),
            ])
            ->having('completed_orders', '>', 0)
            ->orderByDesc('completed_orders')
            ->take(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'stats',
            'fuelStats',
            'towingStats',
            'completionRate',
            'cancellationRate',
            'chartDays',
            'byStatus',
            'peakHours',
            'recentRequests',
            'topProviders',
            'serviceTypeFilter',
        ));
    }

    private function resolveServiceType(User $user): ?string
    {
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
