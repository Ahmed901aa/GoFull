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
            ->where('total_ratings', '>', 0)
            ->with('user')
            ->orderByDesc('average_rating')
            ->take(10)->get();

        $usersByRole = User::select('role', DB::raw('count(*) as total'))
            ->groupBy('role')->pluck('total', 'role');

        return view('admin.analytics.index', compact(
            'byStatus', 'byType', 'perDay', 'topProviders', 'usersByRole'
        ));
    }
}