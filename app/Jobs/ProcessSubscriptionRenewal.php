<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\TenantSubscription;
use App\Services\PayPalService;
use Illuminate\Support\Facades\Log;

class ProcessSubscriptionRenewal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected TenantSubscription $tenantSubscription
    ) {}

    public function handle(PayPalService $paypalService): void
    {
        try {
            // Get latest subscription status from PayPal
            $paypalSubscription = $paypalService->getSubscription($this->tenantSubscription->paypal_subscription_id);
            
            // Update local subscription based on PayPal status  <============================== important  // this is the main job purpose  for paypal .. but for stripe cashier make it automatically
            $this->tenantSubscription->update([
                'status' => strtolower($paypalSubscription->status),
                'paypal_data' => (array) $paypalSubscription
            ]);

            Log::info('Subscription renewal processed', [
                'subscription_id' => $this->tenantSubscription->id,
                'status' => $paypalSubscription->status
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process subscription renewal', [
                'subscription_id' => $this->tenantSubscription->id,
                'error' => $e->getMessage()
            ]);

            $this->fail($e);
        }
    }
}