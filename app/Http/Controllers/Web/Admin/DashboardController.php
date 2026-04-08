<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProviderProfile;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_drivers' => User::where('role', 'driver')->count(),
            'total_providers' => ProviderProfile::where('verification_status', 'approved')->count(),
            'pending_providers' => ProviderProfile::where('verification_status', 'pending')->count(),
            'active_requests' => ServiceRequest::whereIn('status', [
                'pending', 'accepted', 'en_route', 'arrived', 'in_progress',
            ])->count(),
            'completed_today' => ServiceRequest::where('status', 'completed')
                ->whereDate('completed_at', today())->count(),
            'total_requests' => ServiceRequest::count(),
            'total_completed' => ServiceRequest::where('status', 'completed')->count(),
            'revenue_today' => ServiceRequest::where('status', 'completed')
                ->whereDate('completed_at', today())->sum('total'),
        ];

        $recentRequests = ServiceRequest::with(['driver', 'provider.user'])
            ->latest()
            ->take(10)
            ->get();

        $topProviders = ProviderProfile::where('verification_status', 'approved')
            ->with('user')
            ->withCount([
                'serviceRequests as completed_orders' => fn($q) => $q->where('status', 'completed'),
            ])
            ->addSelect(['real_avg_rating' => DB::table('ratings')
                ->join('service_requests', 'service_requests.id', '=', 'ratings.request_id')
                ->whereColumn('service_requests.provider_id', 'provider_profiles.id')
                ->selectRaw('ROUND(AVG(ratings.rating), 1)')
            ])
            ->addSelect(['real_total_ratings' => DB::table('ratings')
                ->join('service_requests', 'service_requests.id', '=', 'ratings.request_id')
                ->whereColumn('service_requests.provider_id', 'provider_profiles.id')
                ->selectRaw('COUNT(*)')
            ])
            ->having('completed_orders', '>', 0)
            ->orderByDesc('completed_orders')
            ->take(5)
            ->get();

        return view('admin.dashboard.index', compact('stats', 'recentRequests', 'topProviders'));
    }
}
