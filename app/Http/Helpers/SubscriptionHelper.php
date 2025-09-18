<?php
// new 
if (!function_exists('user_can_access_feature')) {
    function user_can_access_feature(string $feature, $user = null): bool
    {
        $user = $user ?? auth()->user();
        
        if (!$user) {
            return false;
        }
        
        $subscription = $user->activeSubscription;
        
        if (!$subscription || !$subscription->isActive()) {
            return false;
        }
        
        $featureMap = [
            'basic_features' => ['basic', 'pro', 'enterprise'],
            'advanced_features' => ['pro', 'enterprise'],
            'enterprise_features' => ['enterprise'],
        ];
        
        $allowedPlans = $featureMap[$feature] ?? [];
        
        return in_array(strtolower($subscription->subscriptionPlan->name), $allowedPlans);
    }
}

if (!function_exists('subscription_days_remaining')) {
    function subscription_days_remaining($user = null): ?int
    {
        $user = $user ?? auth()->user();
        
        if (!$user) {
            return null;
        }
        
        $subscription = $user->activeSubscription;
        
        if (!$subscription || !$subscription->ends_at) {
            return null;
        }
        
        return max(0, now()->diffInDays($subscription->ends_at, false));
    }
}