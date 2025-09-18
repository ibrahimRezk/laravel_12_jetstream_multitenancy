<?php
// new for paypal
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TenantSubscription;
use App\Jobs\ProcessSubscriptionRenewal;
use Carbon\Carbon;

class CheckSubscriptionStatuses extends Command
{
    protected $signature = 'subscriptions:check-status';
    protected $description = 'Check and update subscription statuses from PayPal';

    public function handle(): int
    {
        $this->info('Checking subscription statuses...');

        $activeSubscriptions = TenantSubscription::where('status', 'active')
            ->whereNotNull('paypal_subscription_id')
            ->get();

        $this->info("Found {$activeSubscriptions->count()} active subscriptions to check.");

        foreach ($activeSubscriptions as $subscription) {
            ProcessSubscriptionRenewal::dispatch($subscription);
            $this->info("Queued status check for subscription {$subscription->id}");
        }

        $this->info('All subscription status checks queued.');
        return Command::SUCCESS;
    }
}