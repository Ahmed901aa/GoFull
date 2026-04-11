<?php

namespace App\Http\Controllers\API\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class IncomeController extends Controller
{
    /**
     * عرض ملخص الدخل للسائق الحالي
     */
    public function summary(): JsonResponse
    {
        $driver = auth()->user();

        $completedRequests = $driver->serviceRequests()
            ->where('status', 'completed');

        $totalIncome = (clone $completedRequests)->sum('total');
        $totalOrders = (clone $completedRequests)->count();
        $todayIncome = (clone $completedRequests)
            ->whereDate('completed_at', today())
            ->sum('total');
        $todayOrders = (clone $completedRequests)
            ->whereDate('completed_at', today())
            ->count();

        return response()->json([
            'success' => true,
            'data'    => [
                'total_income'  => round($totalIncome, 2),
                'total_orders'  => $totalOrders,
                'today_income'  => round($todayIncome, 2),
                'today_orders'  => $todayOrders,
                'vehicle'       => $driver->vehicle,
            ],
        ]);
    }
}
