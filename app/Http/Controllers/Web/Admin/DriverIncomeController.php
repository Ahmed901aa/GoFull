<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DriverIncomeController extends Controller
{
    public function index(Request $request)
    {
        $serviceType = $this->resolveServiceType();

        $completedBase = ServiceRequest::where('status', 'completed')
            ->when($serviceType, fn ($q) => $q->where('service_type', $serviceType));

        // ── Global Stats ────────────────────────────────────
        $totalIncome = (clone $completedBase)->sum('total');

        $driversWithCompletedOrders = (clone $completedBase)
            ->distinct('driver_id')
            ->count('driver_id');

        // ── Service Type Breakdown ──────────────────────────
        $fuelOrders   = (clone $completedBase)->where('service_type', 'fuel_delivery')->count();
        $towingOrders = (clone $completedBase)->where('service_type', 'towing')->count();
        $fuelIncome   = (clone $completedBase)->where('service_type', 'fuel_delivery')->sum('total');
        $towingIncome = (clone $completedBase)->where('service_type', 'towing')->sum('total');

        // ── Payment Stats ───────────────────────────────────
        $totalPaidOrders = (clone $completedBase)->where('payment_status', 'paid')->count();
        $totalPaidAmount = (clone $completedBase)->where('payment_status', 'paid')->sum('total');
        $cashPaid        = (clone $completedBase)->where('payment_method', 'cash')->where('payment_status', 'paid')->sum('total');

        // ── Top Requesters (drivers who placed the most orders) ─
        $topRequesters = User::where('role', 'driver')
            ->withCount([
                'serviceRequests as total_requests' => fn ($q) => $q->when($serviceType, fn ($q2) => $q2->where('service_type', $serviceType)),
                'serviceRequests as completed_requests' => fn ($q) => $q->where('status', 'completed')->when($serviceType, fn ($q2) => $q2->where('service_type', $serviceType)),
                'serviceRequests as fuel_requests'      => fn ($q) => $q->where('service_type', 'fuel_delivery'),
                'serviceRequests as towing_requests'    => fn ($q) => $q->where('service_type', 'towing'),
            ])
            ->addSelect([
                'total_spent' => ServiceRequest::select(DB::raw('COALESCE(SUM(total), 0)'))
                    ->whereColumn('driver_id', 'users.id')
                    ->where('status', 'completed')
                    ->when($serviceType, fn ($q) => $q->where('service_type', $serviceType)),
            ])
            ->having('total_requests', '>', 0)
            ->orderByDesc('total_requests')
            ->limit(10)
            ->get();

        // ── Drivers Income Table (existing) ─────────────────
        $driversQuery = User::where('role', 'driver')
            ->with('vehicle')
            ->withCount([
                'serviceRequests as completed_orders' => fn ($q) => $q->where('status', 'completed')->when($serviceType, fn ($q2) => $q2->where('service_type', $serviceType)),
            ])
            ->addSelect([
                'total_income' => ServiceRequest::select(DB::raw('COALESCE(SUM(total), 0)'))
                    ->whereColumn('driver_id', 'users.id')
                    ->where('status', 'completed')
                    ->when($serviceType, fn ($q) => $q->where('service_type', $serviceType)),
            ])
            ->having('completed_orders', '>', 0)
            ->orderByDesc('total_income');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $driversQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $drivers = $driversQuery->paginate(20)->withQueryString();

        return view('admin.income.index', compact(
            'totalIncome',
            'driversWithCompletedOrders',
            'fuelOrders',
            'towingOrders',
            'fuelIncome',
            'towingIncome',
            'totalPaidOrders',
            'totalPaidAmount',
            'cashPaid',
            'topRequesters',
            'drivers',
        ));
    }

    public function show(User $driver)
    {
        abort_unless($driver->role === 'driver', 404);

        $serviceType = $this->resolveServiceType();

        $driver->load('vehicle');

        $orders = $driver->serviceRequests()
            ->where('status', 'completed')
            ->when($serviceType, fn ($q) => $q->where('service_type', $serviceType))
            ->with('provider.user')
            ->latest('completed_at')
            ->paginate(20);

        $totalIncome = $driver->serviceRequests()
            ->where('status', 'completed')
            ->when($serviceType, fn ($q) => $q->where('service_type', $serviceType))
            ->sum('total');

        $todayIncome = $driver->serviceRequests()
            ->where('status', 'completed')
            ->when($serviceType, fn ($q) => $q->where('service_type', $serviceType))
            ->whereDate('completed_at', today())
            ->sum('total');

        return view('admin.income.show', compact(
            'driver',
            'orders',
            'totalIncome',
            'todayIncome',
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
