<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WebhookController;
use App\Models\User;
use Inertia\Inertia;
use App\Models\TenantSubscription;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\AdminPlanController;
use App\Http\Controllers\AdminTenantsController;
use App\Http\Controllers\TenantSubscriptionController;
use App\Http\Controllers\AdminSubscriptionController;
use App\Http\Middleware\CheckMainSiteAdminMiddleware;
use Laravel\Cashier\Subscription;



const PAGINATION_COUNT = 10;



// routes/web.php, api.php or any other central route files you have

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {
        // your actual routes
        // dd('here');
        Route::get('/', function () {
            return Inertia::render('Welcome', [
                'canLogin' => Route::has('login'),
                'canRegister' => Route::has('register'),
                'laravelVersion' => Application::VERSION,
                'phpVersion' => PHP_VERSION,
            ]);
        });

        /// for main site admin only
        Route::middleware([
            'web',
            'auth:sanctum',
            config('jetstream.auth_session'),
            'verified',
        ])->group(function () {

            // Route::get('/dashboard', DashboardController::class)->name('dashboard')->middleware(CheckMainSiteAdminMiddleware::class);
            Route::get('/dashboard', function () {

                if (auth()->user()->main_site_admin == true) {
                    return Inertia::render('AdminDashboard' , [
                        'title' => 'dashboard',
                        'routeResourceName' => 'dashboard',
                        // 'breadcrumbs' => $breadcrumbs['breadcrumbs'],

                    ]);
                } else {
                            // $breadcrumbs = Breadcrumbs::render('dashboard');

                    return Inertia::render('TenantDashboard' , [
                        'tenantSubscription' => auth()->user()->tenants[0]->currentSubscription(),
                        'title' => 'dashboard',
                        'routeResourceName' => 'dashboard',
                        // 'breadcrumbs' => $breadcrumbs['breadcrumbs'],

                    ]);
                }
            })->name('dashboard'); 






            /////////////////////////////// admin part /////////////////////////////////////////////////////////////////////////////////////////////////
            // admin controle  Tenant subscriptions
            Route::get('/admin/tenants', [AdminTenantsController::class, 'index'])->name('admin.tenants.index');
            // Route::get('/admin/tenant/{tenantId}/subscription', [AdminTenantsController::class, 'getTenantSubscription'])->name('admin.getTenantSubscription');
            Route::post('/admin/tenant/subscribe', [AdminTenantsController::class, 'subscribe'])->name('admin.tenants.subscribe');
            Route::put('/admin/tenant/{id}', [AdminTenantsController::class, 'changeSubscription'])->name('admin.tenants.changeSubscription');
            // Route::put('/admin/tenant/{tenantId}/subscription/{plan}', [AdminTenantsController::class, 'changeSubscription'])->name('admin.changeSubscription');
            Route::delete('/admin/tenant/{tenantIds}/subscription', [AdminTenantsController::class, 'cancelSubscription'])->name('admin.tenants.cancelSubscription');



            Route::get('/admin/purchase-plans', [AdminPlanController::class, 'index'])->name('admin.plans');
            Route::post('/admin/store-purchase-plans', [AdminPlanController::class, 'store'])->name('admin.plans.store');
            Route::put('/admin/update-purchase-plans/{plan}', [AdminPlanController::class, 'update'])->name('admin.plans.update');
            Route::delete('/admin/delete-purchase-plans/{plan}', [AdminPlanController::class, 'destroy'])->name('admin.plans.destroy');
            ///////////////////////////////end of admin part /////////////////////////////////////////////////////////////////////////////////////////////



            /////////////////////////////// tenant part  for tenants on the main site   .... add middleware to let only tenant owner to access here /////////////////////////////////////////



            Route::get('/tenant/purchase-plans', [TenantSubscriptionController::class, 'plans'])->name('tenant.plans');
            Route::get('addUser', [TenantSubscriptionController::class, 'addUser'])->name('tenant.addUser');/// temporarly for testing     tobe deleted

            Route::get('/tenant/checkout', [TenantSubscriptionController::class, 'checkout'])->name('tenant.checkout');
            Route::get('/tenant/subscription', [TenantSubscriptionController::class, 'getTenantSubscription'])->name('tenant.getTenantSubscription');
            Route::get('tenantSubscriptionDetails', [TenantSubscriptionController::class, 'tenantSubscriptionDetails'])->name('tenantSubscriptionDetails');
            Route::put('/tenant/subscription/{plan}/{tenant}', [TenantSubscriptionController::class, 'changeSubscription'])->name('tenant.changeSubscription'); // check this .. no need for it
            Route::delete('/tenant/cancel_subscription', [TenantSubscriptionController::class, 'cancelSubscription'])->name('tenant.cancelSubscription');
            Route::get('/payment/update', [TenantSubscriptionController::class, 'updatePaymentMethod'])->name('payment.update'); // for cashier stripe

            Route::get('/subscription/retry-upgrade', [TenantSubscriptionController::class, 'retryUpgrade'])->name('subscription.retry-upgrade'); // for cashier stripe



            // Feature-specific routes
            Route::get('/advanced-features', function () {
                return view('tenant.advanced');
            })->middleware('check.subscription:advanced_features')->name('tenant.advanced');

        });
        /////////////////////////////// end of tenant part ///////////////////////////////////////////////////////////////////////////////////////////

    });

}


    //////////// to change lang /////////
    Route::get('/change_lang/{locale}', function ($locale) {
        App::setLocale($locale);
        session()->put('lang', $locale);
        // dd(session('lang'));
        // dd( App::getLocale());
        return redirect()->back();
    })->name('lang');


Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});



/// remember this site does not work with laravel herd   it works well with xampp
/// subsomains starts like this    http://ali.localhost:8000/login ;



// check home.vue
// check trial days here and on stripe.com