<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TenantSubscription;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PayPalWebhookController extends Controller
{
    public function handle(Request $request)
    {



         Log::info('................ PayPal webhook event.....................' );




        // Verify webhook signature (recommended for production)
        // You should implement webhook signature verification for security

        $eventType = $request->input('event_type');
        $resource = $request->input('resource');

        Log::info('PayPal Webhook received', [
            'event_type' => $eventType,
            'resource' => $resource
        ]);

        switch ($eventType) {
            case 'BILLING.SUBSCRIPTION.ACTIVATED':
                $this->handleSubscriptionActivated($resource);
                break;

            case 'BILLING.SUBSCRIPTION.CANCELLED':
                $this->handleSubscriptionCancelled($resource);
                break;

            case 'BILLING.SUBSCRIPTION.EXPIRED':
                $this->handleSubscriptionExpired($resource);
                break;

            case 'BILLING.SUBSCRIPTION.PAYMENT.FAILED':
                $this->handlePaymentFailed($resource);
                break;

            case 'BILLING.SUBSCRIPTION.SUSPENDED':
                $this->handleSubscriptionSuspended($resource);
                break;

            default:
                Log::info('Unhandled PayPal webhook event', ['event_type' => $eventType]);
        }

        return response()->json(['status' => 'success'], 200);
    }

    private function handleSubscriptionActivated($resource)
    {
        $subscriptionId = $resource['id'];
        
        $subscription = TenantSubscription::where('paypal_subscription_id', $subscriptionId)->first();
        
        if ($subscription) {
            $subscription->update([
                'status' => 'active',
                'starts_at' => Carbon::now(),
                'ends_at' => Carbon::now()->addYear()
            ]);

            Log::info('Subscription activated', ['subscription_id' => $subscriptionId]);
        }
    }

    private function handleSubscriptionCancelled($resource)
    {
        $subscriptionId = $resource['id'];
        
        $subscription = TenantSubscription::where('paypal_subscription_id', $subscriptionId)->first();
        
        if ($subscription) {
            $subscription->update([
                'status' => 'cancelled',
                'cancelled_at' => Carbon::now()
            ]);

            Log::info('Subscription cancelled', ['subscription_id' => $subscriptionId]);
        }
    }

    private function handleSubscriptionExpired($resource)
    {
        $subscriptionId = $resource['id'];
        
        $subscription = TenantSubscription::where('paypal_subscription_id', $subscriptionId)->first();
        
        if ($subscription) {
            $subscription->update([
                'status' => 'expired'
            ]);

            Log::info('Subscription expired', ['subscription_id' => $subscriptionId]);
        }
    }

    private function handlePaymentFailed($resource)
    {
        $subscriptionId = $resource['id'];
        
        $subscription = TenantSubscription::where('paypal_subscription_id', $subscriptionId)->first();
        
        if ($subscription) {
            // You might want to suspend or mark for retry
            Log::warning('Payment failed for subscription', [
                'subscription_id' => $subscriptionId,
                'user_id' => $subscription->user_id
            ]);

            // Optionally notify user or suspend subscription
        }
    }

    private function handleSubscriptionSuspended($resource)
    {
        $subscriptionId = $resource['id'];
        
        $subscription = TenantSubscription::where('paypal_subscription_id', $subscriptionId)->first();
        
        if ($subscription) {
            $subscription->update([
                'status' => 'suspended'
            ]);

            Log::info('Subscription suspended', ['subscription_id' => $subscriptionId]);
        }
    }
}