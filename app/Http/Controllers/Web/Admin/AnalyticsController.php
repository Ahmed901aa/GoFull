<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProviderProfile;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $byStatus = ServiceRequest::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status');

        $byType = ServiceRequest::select('service_type', DB::raw('count(*) as total'))
            ->groupBy('service_type')->pluck('total', 'service_type');

        $perDay = ServiceRequest::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as total')
        )
        ->where('created_at', '>=', now()->subDays(30))
        ->groupBy('date')->orderBy('date')->get();

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
            ->take(10)->get();

        $usersByRole = User::select('role', DB::raw('count(*) as total'))
            ->groupBy('role')->pluck('total', 'role');

        $revenue = [
            'total' => ServiceRequest::where('status', 'completed')->sum('total'),
            'today' => ServiceRequest::where('status', 'completed')
                ->whereDate('completed_at', today())->sum('total'),
            'this_month' => ServiceRequest::where('status', 'completed')
                ->whereMonth('completed_at', now()->month)
                ->whereYear('completed_at', now()->year)->sum('total'),
        ];

        return view('admin.analytics.index', compact(
            'byStatus', 'byType', 'perDay', 'topProviders', 'usersByRole', 'revenue'
        ));
    }
}