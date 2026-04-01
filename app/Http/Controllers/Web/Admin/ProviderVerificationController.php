<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SetAppointmentRequest;
use App\Models\ProviderProfile;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProviderVerificationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $providers = ProviderProfile::where('verification_status', $status)
            ->with(['user', 'documents'])
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
        return view('admin.providers.show', compact('provider'));
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
}