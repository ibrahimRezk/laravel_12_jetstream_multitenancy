<?php

namespace App\Observers;

use App\Models\TenantSubscription;
use App\Notifications\SubscriptionStatusChanged;
use Illuminate\Support\Facades\Log;
 // new 
class TenantSubscriptionObserver
{
    public function updated(TenantSubscription $subscription): void
    {
        if ($subscription->isDirty('status')) {
            $previousStatus = $subscription->getOriginal('status');
            
            Log::info('Subscription status changed', [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
                'from' => $previousStatus,
                'to' => $subscription->status
            ]);
            
            // Send notification to user
            $subscription->user->notify(
                new SubscriptionStatusChanged($subscription, $previousStatus)
            );
        }
    }
}
