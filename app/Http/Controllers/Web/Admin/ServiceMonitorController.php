<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\DB;

class ServiceMonitorController extends Controller
{
    public function index()
    {
        $serviceType = $this->resolveServiceType();

        $activeStatuses = ['pending', 'accepted', 'en_route', 'arrived', 'in_progress'];

        $requests = ServiceRequest::whereIn('status', $activeStatuses)
            ->when($serviceType, fn ($q) => $q->where('service_type', $serviceType))
            ->with(['driver', 'provider.user'])
            ->latest()
            ->paginate(20);

        // Status breakdown for active requests
        $statusCounts = ServiceRequest::whereIn('status', $activeStatuses)
            ->when($serviceType, fn ($q) => $q->where('service_type', $serviceType))
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $totalActive = $statusCounts->sum();

        // Type breakdown
        $fuelActive = ServiceRequest::whereIn('status', $activeStatuses)
            ->when($serviceType, fn ($q) => $q->where('service_type', $serviceType))
            ->where('service_type', 'fuel_delivery')
            ->count();

        $towingActive = ServiceRequest::whereIn('status', $activeStatuses)
            ->when($serviceType, fn ($q) => $q->where('service_type', $serviceType))
            ->where('service_type', 'towing')
            ->count();

        return view('admin.monitor.index', compact('requests', 'statusCounts', 'totalActive', 'fuelActive', 'towingActive'));
    }

    public function show(ServiceRequest $request)
    {
        $request->load(['driver', 'provider.user', 'rating']);

        return view('admin.monitor.show', compact('request'));
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
