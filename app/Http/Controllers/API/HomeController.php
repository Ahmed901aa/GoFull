<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * GET /api/home
     * Returns home page data: banners + active order (if any).
     */
    public function index(Request $request)
    {
        $banners = Banner::active()->get();

        $activeOrder = null;
        if ($request->user()) {
            $activeOrder = ServiceRequest::where('driver_id', $request->user()->id)
                ->whereIn('status', ['accepted', 'en_route', 'arrived', 'in_progress'])
                ->with('provider.user')
                ->latest()
                ->first();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'banners' => $banners,
                'active_order' => $activeOrder,
            ],
        ]);
    }
}
