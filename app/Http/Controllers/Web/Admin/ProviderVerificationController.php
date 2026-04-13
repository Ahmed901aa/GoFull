<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SetAppointmentRequest;
use App\Models\ProviderProfile;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProviderVerificationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $serviceType = $request->get('service_type');

        // Employees only see providers matching their type
        $employeeFilter = $this->resolveServiceType();
        if ($employeeFilter) {
            $serviceType = $employeeFilter;
        }

        $providers = ProviderProfile::where('verification_status', $status)
            ->when($serviceType, fn ($q) => $q->where('service_type', $serviceType))
            ->with(['user', 'documents'])
            ->withCount([
                'serviceRequests as completed_orders' => fn($q) => $q->where('status', 'completed'),
                'serviceRequests as cancelled_orders' => fn($q) => $q->where('status', 'cancelled'),
                'serviceRequests as total_orders',
            ])
            // Real rating from ratings table
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
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'pending'         => ProviderProfile::where('verification_status', 'pending')->count(),
            'appointment_set' => ProviderProfile::where('verification_status', 'appointment_set')->count(),
            'approved'        => ProviderProfile::where('verification_status', 'approved')->count(),
            'rejected'        => ProviderProfile::where('verification_status', 'rejected')->count(),
        ];

        return view('admin.providers.index', compact('providers', 'counts', 'status'));
    }

    public function show(ProviderProfile $provider)
    {
        $provider->load(['user', 'documents', 'verifiedBy']);
        $provider->loadCount([
            'serviceRequests as completed_orders' => fn($q) => $q->where('status', 'completed'),
            'serviceRequests as cancelled_orders' => fn($q) => $q->where('status', 'cancelled'),
            'serviceRequests as total_orders',
        ]);

        // Real rating computed from ratings table
        $ratingStats = DB::table('ratings')
            ->join('service_requests', 'service_requests.id', '=', 'ratings.request_id')
            ->where('service_requests.provider_id', $provider->id)
            ->selectRaw('ROUND(AVG(ratings.rating), 1) as avg_rating, COUNT(*) as total_ratings')
            ->first();

        $provider->real_avg_rating = $ratingStats->avg_rating ?? 0;
        $provider->real_total_ratings = $ratingStats->total_ratings ?? 0;

        $totalRevenue = $provider->serviceRequests()->where('status', 'completed')->sum('total');
        $recentOrders = $provider->serviceRequests()
            ->with(['driver', 'rating'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.providers.show', compact('provider', 'totalRevenue', 'recentOrders'));
    }

    public function setAppointment(SetAppointmentRequest $request, ProviderProfile $provider)
    {
        $provider->update([
            'verification_status' => 'appointment_set',
            'appointment_date'    => $request->appointment_date,
            'appointment_notes'   => $request->appointment_notes,
        ]);

        $formatted = Carbon::parse($request->appointment_date)->format('D, d M Y \a\t h:i A');

        NotificationService::send(
            $provider->user,
            'Appointment Scheduled',
            "Your verification appointment has been set for {$formatted}.",
            ['appointment_date' => $request->appointment_date]
        );

        return back()->with('success', 'Appointment scheduled successfully.');
    }

    public function approve(ProviderProfile $provider)
    {
        $provider->update([
            'verification_status' => 'approved',
            'verified_at'         => now(),
            'verified_by'         => auth()->id(),
            'rejection_reason'    => null,
        ]);

        NotificationService::send(
            $provider->user,
            'Account Approved',
            'Congratulations! Your account has been verified. You can now start accepting requests.',
            []
        );

        return back()->with('success', "Provider '{$provider->user->name}' approved successfully.");
    }

    public function reject(Request $request, ProviderProfile $provider)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $provider->update([
            'verification_status' => 'rejected',
            'rejection_reason'    => $request->rejection_reason,
        ]);

        NotificationService::send(
            $provider->user,
            'Account Rejected',
            'Your verification request has been rejected. Reason: ' . $request->rejection_reason,
            []
        );

        return back()->with('success', "Provider '{$provider->user->name}' rejected.");
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
