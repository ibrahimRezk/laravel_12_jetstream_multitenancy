<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\TenantSubscription;
use Inertia\Inertia;
use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Services\PlanService;
use App\Services\StripePaymentService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\TenantSubscriptionResource;
use Laravel\Cashier\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


use App\Services\PayPalService;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class TenantSubscriptionController extends Controller
{
    private string $routeResourceName = 'tenant';

    protected $planService;

    public function __construct(PlanService $planService , protected PayPalService $paypalService)
    {
        $this->planService = $planService;
                // $this->middleware('auth');

    }


    public function plans(Request $request)
    {
        $tenant = auth()->user()->tenants()->first();

        $subscription = $tenant->subscription()->latest()->first();

        $plan = Plan::find($tenant->plan_id);

        $planChoosed = false;
        $planPaid = false;

        $tenantSubscription = TenantSubscription::where('tenant_id', $tenant->id)->where('plan_id', $tenant->plan_id)->first();

        if ($tenantSubscription) {
            $planChoosed = true;
        }

        if ($plan != null) {
            //////////// check payment method for queries  //////////////////////////////
            ////////// if payment method is stripe//////////
            $stripeSubscription = Subscription::where('user_id', auth()->user()->id)
                // ->where('type', $plan->product_id_on_stripe)
                ->where('stripe_price', $plan->price_id_on_stripe)
                ->where('stripe_status', '!=', 'canceled')
                ->first();


            if ($stripeSubscription) {
                $planPaid = true;
            }
        }




        try {
            $plans = $this->planService->getAvailablePlans();
            // dd($plan);
            return Inertia::render('Plans', [
                'type' => $request->type,   /// tyes  select for first time , or change plan
                'planChoosed' => $planChoosed,
                'routeResourceName' => $this->routeResourceName,
                'planPaid' => $planPaid,
                'plan' => $plan ?? null,
                'subscription' => $subscription ?? null,
                'tenantId' => auth()->user()->tenants[0]?->id ?? null,
                'plans' => $plans->map(function ($plan) {
                    return [
                        'id' => $plan->id,
                        'name' => $plan->name,
                        'description' => $plan->description,
                        'price_id_on_stripe' => $plan->price_id_on_stripe,
                        'product_id_on_stripe' => $plan->product_id_on_stripe,
                        'paypal_product_id' => $plan->paypal_product_id,
                        'paypal_plan_id' => $plan->paypal_plan_id,
                        'price' => $plan->price,
                        'currency' => $plan->currency,
                        'interval' => $plan->interval,
                        'features' => $plan->features,
                        'trial_days' => $plan->trial_days,
                        'sort_order' => $plan->sort_order
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch plans: ' . $e->getMessage()
            ], 500);
        }
    }


    public function addUser()
    {
        $time = time();
        $user = User::create([
            'name' => $time . 'kamil',
            'email' => $time . 'kamil@hotmail.com',
            'password' => Hash::make('55555sssss'),
        ]);


        $user->tenants()->attach(auth()->user()->tenants[0]?->id); /// very important line to attatch users with there tenants and we control access to only this tenant  from CheckTenantUserMiddleware 


    }




    public function checkout(Request $request)
    {
        $tenant = auth()->user()->tenants[0];

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }


        //////////////////////////////// check payment method for queries ////////////////////////////////////////

        // CHECK if the tenant has active subscription on stripe   "subscriptions" table     and here   "tenant_subscriptions" table
        //// stripe subscription 
        $stripeSubscription = Subscription::where('user_id', auth()->user()->id)->where('stripe_status', 'active')->first(); /// check active it may be another status like past_due فات موعد استحقاقها

        /// our site subscription 
        $newPlan = Plan::find($request->plan_id);
        $oldPlan = null;
        if ($stripeSubscription) {
            $oldPlan = Plan::where('price_id_on_stripe', $stripeSubscription->stripe_price)->first();
        }

        if ($oldPlan == $newPlan) {
            // if ($oldPlan == $newPlan) {
            return back()->with('error', 'Tenant already has an active subscription');
        }

        $changeSubscription = false;  // for upgrade or downgrade 
        if ($oldPlan && $stripeSubscription && $stripeSubscription->stripe_price != $newPlan->price_id_on_stripe) {
            $changeSubscription = true;
        }


        try {
            if($request->pay_with == 'paypal'){
                $paypalService = new PayPalService();
                $result =  $paypalService->createSubscription($newPlan->paypal_plan_id);
                $payment_url = $result->links[0]['href'];
                
                return redirect()->away($payment_url);

            }elseif($request->pay_with == 'stripe')
            {
                return StripePaymentService::processRecurringPayment($newPlan, $changeSubscription);
            }

            // after payment success there is webhook event comes from stripe  and stripewebhook listener  and in case of success it will complete subscription in the listener
            //////////// end         //// check payment method for queries ////////////////////////////////////////

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to subscribe: ' . $e->getMessage()
            ], 500);
        }
    }


    public function getTenantSubscription(Request $request)
    {
        try {
            $tenant = auth()->user()->tenants[0];
            if (!$tenant) {
                return back()->with('error', 'Tenant not found');
            }

            $subscription = $tenant->subscription()->latest('updated_at')->first();

            if ($subscription == null) {
                return back()->with('error', 'no subscription found for this tenant');
            }

            $subscription->load('plan');


            $subscription = new TenantSubscriptionResource($subscription);
            if (!$subscription) {
                return response()->json([
                    'success' => true,
                    'subscription' => null
                ]);
            }

            // dd($subscription);


            return Inertia::render('Subscription', [
                'subscription' => $subscription
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subscription: ' . $e->getMessage()
            ], 500);
        }
    }


    // Route to update payment method
    public function updatePaymentMethod()
    {
        $user = auth()->user();

        // check payment method for queries
        // Store the pending upgrade in session
        $pendingUpgrade = session('pending_upgrade');
        $returnUrl = $pendingUpgrade
            ? route('tenant.subscription.retry-upgrade')
            : route('dashboard');

        return $user->redirectToBillingPortal($returnUrl);
    }



    // Retry the upgrade after payment method update
    public function retryUpgrade()
    {
        $pendingUpgrade = session('pending_upgrade');

        if (!$pendingUpgrade) {
            return redirect()->route('dashboard')
                ->with('error', 'No pending upgrade found.');
        }

        // Clear the session and retry
        session()->forget('pending_upgrade');

        // check payment method for queries
        return StripePaymentService::processRecurringPayment($pendingUpgrade, true);

        // return $this->changePlan(new Request(['price_id' => $pendingUpgrade]));
    }

    /**
     * Cancel tenant's subscription
     */
    public function cancelSubscription(Request $request)
    {
        try {



            $tenant = auth()->user()->tenants[0];
            if (!$tenant) {
                return back()->with('error', 'Tenant not found');
            }
            // $tenant = Tenant::findOrFail(tenant('id'));
            $subscription = $this->planService->cancelSubscription($tenant);



            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active subscription found'
                ], 404);
            }

            return back()->with('success', 'canceled succcessfully');


        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel subscription: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get tenant's current subscription
     */


    // public function tenantSubscriptionDetails()
    // {
    //     return Tenant::findOrFail(auth()->user()->tenants[0]?->id)?->subscription;
    // }







}