<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Services\PlanService;
use Illuminate\Http\JsonResponse;
use App\Models\TenantSubscription;
use App\Http\Controllers\Controller;
use App\Http\Requests\TenantRequest;
use App\Http\Resources\PlanResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\TenantResource;
use App\Http\Requests\AddTenantRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Http\Resources\TenantSubscriptionResource;


class AdminTenantsController extends Controller
{
    private string $routeResourceName = 'admin.tenants';


    protected $planService;

    public function __construct(PlanService $planService)
    {
        $this->planService = $planService;
    }


    public function index(Request $request)
    {
        try {
            $tenants = Tenant::with([
                'subscription',
                'plan',
                'subscriptions',
                'owner:id,name,email',
                'users:id,name,email'
            ])
                // ->when($request->name, fn(Builder $builder, $name) => $builder->where('name', 'like', "%{$name}%"))

                ->whereHas(
                'users', // to be changed to user only or owner
                fn ($query) =>
                $query->when($request->name, fn (Builder $builder, $name) => $builder->where('name', 'like', "%{$name}%")),
            )



                ->orderBy('id')
                // ->get();
                // ->paginate(1);
                ->paginate(10)->onEachSide(2)->appends(request()->query());

            // dd($tenants);
            $plans = PlanResource::collection(Plan::get());


            // dd($tenants[0]->currentSubscription() );
            return Inertia::render('AllTenants/Index', [
                'title' => 'all tenants',

                'items' => TenantResource::collection($tenants),
                'plans' => $plans,
                'routeResourceName' => $this->routeResourceName,
                'filters' => (object) $request->all(),
                'method' => 'index', // used in composable filters
                // 'type' => $plans 
                'headers' => [
                    [
                        'label' => '#',
                        'name' => '#',
                    ],
                    [
                        'label' => 'name',
                        'name' => 'name',
                    ],
                    [
                        'label' => 'email',
                        'name' => 'email',
                    ],
                    [
                        'label' => 'status',
                        'name' => 'status',
                    ],
                    [
                        'label' => 'plan',
                        'name' => 'plan',
                    ],
                    [
                        'label' => 'interval',
                        'name' => 'interval',
                    ],
                    [
                        'label' => 'price',
                        'name' => 'price',
                    ],
                    [
                        'label' => 'created at',
                        'name' => 'created at',
                    ],
                    [
                        'label' => 'ends at',
                        'name' => 'ends at',
                    ],
                    [
                        'label' => 'trial ends at',
                        'name' => 'trial ends at',
                    ],
                    [
                        'label' => 'actions',
                        'name' => 'actions',
                    ],
                ],

            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tenants: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Store and Subscribe tenant to a plan
     */
    public function store(TenantRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
            ]);

            $tenant = Tenant::create(
                [
                    'tenancy_db_name' => $request['subdomain'],  // must add this part other wise duplicate name error will appear
                    'owner_id' => $user->id,  // must add this part other wise duplicate name error will appear
                    'plan_id' => null,
                    'tenant_subscription_id' => null


                ]
            );

            $tenant->domains()->create([
                'domain' => $request['subdomain'] // // if you use initializebysubdomain middleware in tenant.php
            ]);

            $user->tenants()->attach($tenant->id);

            $plan = Plan::find($request->plan_id);


            $this->planService->subscribeTenant($tenant, $plan);

            return back()->with('success', 'created successfully');



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


    public function update(TenantRequest $request, Tenant $id)
    {

        $tenant = Tenant::findOrFail($request->id);
        $plan = Plan::findOrFail($request->plan_id);


        try {

            $user = User::find($tenant->owner_id);

            $data = $request->safe()->only(['email', 'name']);
            $request->password !== null ? $data['password'] = bcrypt($request->safe()->password) : '';
            $user->update($data);


            $tenant->tenancy_db_name = $request['subdomain']; /// db name update
            $tenant->plan_id = $request['plan_id'];
            $tenant->save();


            $domainModel = $tenant->domains[0];
            $domainModel->domain = $request['subdomain'];  /// subdomain update
            $domainModel->save();



            // Cancel existing subscription
            $this->planService->cancelSubscription($tenant);

            // Create new subscription
            $this->planService->subscribeTenant($tenant, $plan);

            return back()->with('success', 'created successfully');


        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to change subscription: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Cancel tenant's subscription
     */

    public function cancelSubscription($id)
    {
        try {

            $tenant = Tenant::findOrFail($id);
            $cancelSubscription = $this->planService->cancelSubscription($tenant);

            return redirect()->back()->with('success', 'canceled successfully');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel subscription: ' . $e->getMessage()
            ], 500);
        }
    }





}