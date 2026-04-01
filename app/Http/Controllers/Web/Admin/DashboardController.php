<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProviderProfile;
use App\Models\ServiceRequest;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_drivers'     => User::where('role', 'driver')->count(),
            'total_providers'   => ProviderProfile::where('verification_status', 'approved')->count(),
            'pending_providers' => ProviderProfile::where('verification_status', 'pending')->count(),
            'active_requests'   => ServiceRequest::whereIn('status', [
                'pending', 'accepted', 'en_route', 'arrived', 'in_progress'
            ])->count(),
            'completed_today'   => ServiceRequest::where('status', 'completed')
                ->whereDate('completed_at', today())->count(),
            'total_requests'    => ServiceRequest::count(),
        ];

        $recentRequests = ServiceRequest::with(['driver', 'provider.user'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentRequests'));
    }
}