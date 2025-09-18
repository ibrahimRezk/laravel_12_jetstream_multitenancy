<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Observers\TenantSubscriptionObserver;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class TenantSubscription extends Model  // this to get all subscriptions history for tenant  including current and previous subscriptions
{
    use BelongsToTenant;  //// notice this trait

//     public static function boot(): void
// {
//     TenantSubscription::observe(TenantSubscriptionObserver::class); // new 
// }

    protected $fillable = [
        'tenant_id',
        'plan_id',
        'notes',
        'status',
        'price',
        'trial_ends_at',
        'ends_at',
        'created_at',
        'updated_at' ,

        'paypal_subscription_id', // new
        'starts_at', // new
        'cancelled_at', // new
        'paypal_data', // new
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'ends_at' => 'datetime',

        'starts_at' => 'datetime', // new
        'cancelled_at' => 'datetime', // new
        'paypal_data' => 'array' // new

    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function isActive()
    {
        return $this->status === 'active' &&
            ($this->ends_at === null || $this->ends_at->isFuture());
    }
   

        public function isExpired(): bool  // new 
    {
        return $this->ends_at && $this->ends_at->isPast();
    }


    public function onTrial()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }
}
