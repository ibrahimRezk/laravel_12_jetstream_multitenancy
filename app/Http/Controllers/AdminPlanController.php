<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Services\PlanService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\PlanRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\TenantRequest;
use App\Http\Resources\PlanResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\TenantResource;
use App\Http\Requests\AddTenantRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Http\Resources\TenantSubscriptionResource;


use Stripe\Stripe;
use Stripe\Price;
use Laravel\Cashier\Cashier;
use Stripe\Plan as StripePlan;

class AdminPlanController extends Controller
{
    private string $routeResourceName = 'admin.plans';

    protected $planService;


    public function __construct(PlanService $planService)
    {
        // parent::__construct();
        $this->planService = $planService;

    }

    /**
     * Get all available purchase plans
     */
    public function index()
    {




        try {
            $plans = $this->planService->getAvailablePlans();
            $popularPlan = $this->planService->getPopularPlan();
            return Inertia::render('AdminControlPlans', [
                'title' => 'plans',
                'method' => 'index',
                'tenants' => auth()->user()->tenants, //added only to the admin  because if the tenant choose a plan we already know his id by tenant('id)
                'popularPlan' => $popularPlan ? [
                    'id' => $popularPlan->plan_id,
                    'occurrences' => $popularPlan->occurrences
                ] :
                    [
                        'id' => Plan::first()->id,
                        'occurrences' => 0,
                    ],


                'routeResourceName' => $this->routeResourceName,

                'plans' => $plans->map(function ($plan) {
                    return [
                        'id' => $plan->id,
                        'name' => $plan->name,
                        'description' => $plan->description,
                        'price' => (double) $plan->price * 1,
                        'currency' => $plan->currency,
                        // 'price_id_on_stripe' => $plan->price_id_on_stripe,
                        // 'product_id_on_stripe' => $plan->product_id_on_stripe,
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
    public function store(PlanRequest $request)
    {


        $plansCount = Plan::count();
        $modifiedFeatures = [];
        foreach ($request->features as $feature) {
            $feature = str_replace(' ', '_', $feature);
            $modifiedFeatures[] = $feature;
        }

        $data['name'] = $request->name;
        $data['description'] = $request->description;
        $data['price'] = $request->price;
        // $data['price_id_on_stripe'] = $request->price_id_on_stripe;
        // $data['product_id_on_stripe'] = $request->product_id_on_stripe;

        $data['currency'] = $request->currency;
        $data['interval'] = $request->interval;
        $data['features'] = $modifiedFeatures;
        $data['trial_days'] = $request->trial_days;
        $data['sort_order'] = $plansCount + 1; // Increment sort order based on existing plans

        $plan = Plan::create($data);





        ///// stripe section ////////////////////////////////////////////////////


        Stripe::setApiKey(env('STRIPE_SECRET'));

        $stripePlan = StripePlan::create([
            'amount' => $request->price * 100, // Amount in cents so you have to put *100 to be in dollars for example
            'interval' => $request->interval, // or 'year', 'week', 'day'
            'product' => [
                'name' => $request->name,
            ],
            'currency' => $request->currency,
            // '' => 'gold-plan-id', // Optional: provide a unique ID for the plan
        ]);

        // dd($stripePlan);

        $plan->update(['price_id_on_stripe' => $stripePlan->id, 'product_id_on_stripe' => $stripePlan->product]);


        ////////////////////////////////// end stripe section /////////////////////////////////////////


        return back()->with('success', 'Plan created successfully.');


    }
    public function update(PlanRequest $request, Plan $plan)
    {



        $modifiedFeatures = [];
        foreach ($request->features as $feature) {
            $feature = str_replace(' ', '_', $feature);
            $modifiedFeatures[] = $feature;
        }

        $data['name'] = $request->name;
        $data['description'] = $request->description;
        // $data['price_id_on_stripe'] = $request->price_id_on_stripe;
        // $data['product_id_on_stripe'] = $request->product_id_on_stripe;

        $data['price'] = $request->price;
        $data['currency'] = $request->currency;
        $data['interval'] = $request->interval;
        $data['features'] = $modifiedFeatures;
        $data['trial_days'] = $request->trial_days;


        /////////////// to change price on stripe //////////////////////////////

        //Stripe Plans and Prices are designed to be immutable once created, meaning you cannot directly "edit" the price of an existing plan or price object through the Stripe API, including when using Laravel Cashier.
        // However, you can achieve the effect of changing a plan's price by implementing the following steps using the Stripe API and Laravel:
        // Create a New Price: Create a new Stripe Price object with the desired new price and associate it with the same Product as the original plan. This can be done using the Stripe PHP client library within your Laravel application.

        ///// with laravel cashier //////////////////////
        // Create new price

        // Get Cashier's configured Stripe client
        $stripe = Cashier::stripe();

        // Create new price using Cashier's Stripe instance
        $newPrice = $stripe->prices->create([
            'unit_amount' => $request->price * 100 , //  Convert to cents
            'currency' => $request->currency,
            'recurring' => [
                'interval' => $request->interval,
            ],
            'product' => $plan->product_id_on_stripe,
        ]);

        // Deactivate old prices
        $oldPrices = $stripe->prices->all([
            'product' => $plan->product_id_on_stripe,
            'active' => true,
        ]);

        foreach ($oldPrices->data as $oldPrice) {
            if ($oldPrice->id !== $newPrice->id) {
                $stripe->prices->update($oldPrice->id, [
                    'active' => false,
                ]);
            }
        }

        // dd($newPrice);

        // get users with this subscription and move them to the new price 
        $tenants = Tenant::all();
        foreach ($tenants as $tenant) {
            if ($tenant->plan_id == $plan->id) {
                $user = $tenant->owner;
                // $user = $tenant->users[0];

                // $user->subscription('default')->swap($newPrice->id);


                $newPriceId = $newPrice->id;
                $oldSubscription = $user->subscriptions()->where('stripe_status', 'active')->first();

                if (!$oldSubscription) {
                    continue;
                }
                $oldSubscription->swap($newPriceId);

            }

        }


        ////////////////////////////////// stripe end ///////////////////////////////////////////////////////////////

    




        $data['price_id_on_stripe'] = $newPrice->id;
        $data['product_id_on_stripe'] = $newPrice->product;
        $plan->update($data);


        return back()->with('success', 'Plan updated successfully.');
        // $feature = str_replace(' ', '_', $feature);
    }
    public function destroy($ids)
    {
        $all_ids = explode(',', $ids);

        foreach ($all_ids as $id) {
            // first check if there is any active subscription to this plan
            $plan = Plan::find($id);

            $plan->delete();
        }
        return redirect()->back()->with('success', 'item deleted successfully');

    }
}