<?php

namespace App\Console\Commands;

use App\Events\OrderCancelled;
use App\Models\AppSetting;
use App\Models\ServiceRequest;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class ExpireStaleRequests extends Command
{
    protected $signature = 'orders:expire';

    protected $description = 'Cancel pending requests that no provider accepted within the timeout window';

    public function handle(): int
    {
        // Admin-configurable via app_settings. Emergency orders expire
        // faster: a stranded customer should be re-prompted to retry (or
        // call support) sooner than the normal window.
        $timeoutMinutes = (int) AppSetting::getValue('request_timeout_minutes', 15);
        $emergencyMinutes = (int) AppSetting::getValue('emergency_timeout_minutes', 10);

        $stale = ServiceRequest::where('status', 'pending')
            ->whereNull('provider_id')
            ->where(function ($q) use ($timeoutMinutes, $emergencyMinutes) {
                $q->where(fn ($n) => $n->where('is_emergency', false)
                        ->where('created_at', '<=', now()->subMinutes($timeoutMinutes)))
                  ->orWhere(fn ($e) => $e->where('is_emergency', true)
                        ->where('created_at', '<=', now()->subMinutes($emergencyMinutes)));
            })
            ->get();

        foreach ($stale as $request) {
            // Conditional update — skip if a provider accepted meanwhile
            $affected = ServiceRequest::where('id', $request->id)
                ->where('status', 'pending')
                ->whereNull('provider_id')
                ->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                    'cancelled_by' => 'system',
                    'cancellation_reason' => 'لم يتم العثور على مزود خدمة متاح — انتهت مهلة الطلب',
                ]);

            if ($affected === 0) {
                continue;
            }

            $request->refresh();

            NotificationService::send(
                $request->driver,
                'Request Expired',
                'No provider was available for your request. Please try again.',
                ['request_id' => $request->id, 'status' => 'cancelled']
            );

            broadcast(new OrderCancelled($request));
        }

        $this->info("Expired {$stale->count()} stale request(s).");

        return self::SUCCESS;
    }
}
