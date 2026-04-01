<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;

class ServiceMonitorController extends Controller
{
    public function index()
    {
        $requests = ServiceRequest::whereIn('status', [
            'pending', 'accepted', 'en_route', 'arrived', 'in_progress'
        ])
        ->with(['driver', 'provider.user'])
        ->latest()
        ->paginate(20);

        return view('admin.monitor.index', compact('requests'));
    }

    public function show(ServiceRequest $request)
    {
        $request->load(['driver', 'provider.user', 'rating']);
        return view('admin.monitor.show', compact('request'));
    }
}