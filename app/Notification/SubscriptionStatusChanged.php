<?php

namespace App\Notifications;
// new
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\TenantSubscription;

class SubscriptionStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected TenantSubscription $subscription,
        protected string $previousStatus
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $planName = $this->subscription->subscriptionPlan->name;
        
        return match($this->subscription->status) {
            'active' => (new MailMessage)
                ->subject('Subscription Activated')
                ->greeting('Great news!')
                ->line("Your {$planName} subscription has been activated.")
                ->line('You now have access to all plan features.')
                ->action('View Dashboard', url('/dashboard')),
                
            'cancelled' => (new MailMessage)
                ->subject('Subscription Cancelled')
                ->greeting('Subscription Cancelled')
                ->line("Your {$planName} subscription has been cancelled.")
                ->line('You will continue to have access until your current billing period ends.')
                ->action('View Plans', route('subscriptions.index')),
                
            'expired' => (new MailMessage)
                ->subject('Subscription Expired')
                ->greeting('Subscription Expired')
                ->line("Your {$planName} subscription has expired.")
                ->line('Please renew your subscription to continue using premium features.')
                ->action('Renew Subscription', route('subscriptions.index')),
                
            default => (new MailMessage)
                ->subject('Subscription Status Update')
                ->line("Your {$planName} subscription status has been updated to: " . ucfirst($this->subscription->status))
                ->action('View Account', url('/dashboard'))
        };
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'subscription_id' => $this->subscription->id,
            'plan_name' => $this->subscription->subscriptionPlan->name,
            'status' => $this->subscription->status,
            'previous_status' => $this->previousStatus,
            'message' => "Your {$this->subscription->subscriptionPlan->name} subscription is now {$this->subscription->status}"
        ];
    }
}
