<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;

class AppSettingController extends Controller
{
    /**
     * GET /api/app/settings
     * Returns all app settings as key-value pairs.
     */
    public function index()
    {
        $settings = AppSetting::all()->pluck('value', 'key');

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }
}
