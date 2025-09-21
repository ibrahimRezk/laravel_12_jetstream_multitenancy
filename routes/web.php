<?php

use App\Models\User;
use Inertia\Inertia;
use Laravel\Cashier\Subscription;
use App\Models\TenantSubscription;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\AdminPlanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminTenantsController;
use App\Http\Controllers\AdminSubscriptionController;
use App\Http\Middleware\CheckMainSiteAdminMiddleware;
use App\Http\Controllers\TenantSubscriptionController;



const PAGINATION_COUNT = 10;



// routes/web.php, api.php or any other central route files you have

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {
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
                    return Inertia::render('AdminDashboard', [
                        'title' => 'dashboard',
                        'routeResourceName' => 'dashboard',
                        // 'breadcrumbs' => $breadcrumbs['breadcrumbs'],
                    ]);
                } else {
                    // $breadcrumbs = Breadcrumbs::render('dashboard');
                    return Inertia::render('TenantDashboard', [
                        'tenantSubscription' => auth()->user()->tenants[0]->currentSubscription(),
                        'title' => 'dashboard',
                        'routeResourceName' => 'dashboard',
                        // 'breadcrumbs' => $breadcrumbs['breadcrumbs'],
                    ]);
                }
            })->name('dashboard');






            /////////////////////////////// admin part /////////////////////////////////////////////////////////////////////////////////////////////////
            // admin  Tenants and subscriptions controller
            Route::middleware(CheckMainSiteAdminMiddleware::class)->name('admin.')->group(function () {
                Route::delete('/tenant/{tenantIds}/subscription', [AdminTenantsController::class, 'cancelSubscription'])->name('tenants.cancelSubscription');
                Route::resource('tenants', AdminTenantsController::class);

                // admin plan controler
                Route::resource('plans', AdminPlanController::class);
            });
            ///////////////////////////////end of admin part /////////////////////////////////////////////////////////////////////////////////////////////







            ///////////////////// tenant part  for tenants on the main site   .... add middleware to let only tenant owner to access here /////////////////

            Route::name('tenant.')->group(function () {
                Route::get('addUser', [TenantSubscriptionController::class, 'addUser'])->name('addUser');/// temporarly for testing     tobe deleted

                Route::get('/tenant/purchase-plans', [TenantSubscriptionController::class, 'plans'])->name('plans.index');
                Route::get('/tenant/checkout', [TenantSubscriptionController::class, 'checkout'])->name('checkout');
                Route::get('/tenant/subscription', [TenantSubscriptionController::class, 'getTenantSubscription'])->name('getTenantSubscription');
                

                Route::delete('/tenant/cancel_subscription', [TenantSubscriptionController::class, 'cancelSubscription'])->name('cancelSubscription');
                Route::get('/payment/update', [TenantSubscriptionController::class, 'updatePaymentMethod'])->name('payment.update'); // for cashier stripe
                Route::get('/subscription/retry-upgrade', [TenantSubscriptionController::class, 'retryUpgrade'])->name('subscription.retry-upgrade'); // for cashier stripe


                // Feature-specific routes
                Route::get('/advanced-features', function () {
                    return view('tenant.advanced');
                })->middleware('check.subscription:advanced_features')->name('advanced');





                // check if this is used any way
                // Route::get('tenantSubscriptionDetails', [TenantSubscriptionController::class, 'tenantSubscriptionDetails'])->name('tenantSubscriptionDetails');



            });





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




////////run php artisan cashier:webhook 
///// then run =>  stripe listen --forward-to http://laraveljetstreamshadcntailwind4multitenancy.test/stripe/webhook
///// or then run =>  stripe listen --forward-to http://laravel_12_jetstream_multitenancy.test/stripe/webhook     depends on the site


    

/// remember this site does not work with laravel herd   it works well with xampp
/// subsomains starts like this    http://ali.localhost:8000/login ;
//  ليست كل العملات مسموح بها في باي بال يجب ان تكون العملة مضافة اولا على باي بال على ادارة حساب البزنس - الحساب  المدفوعات والحسابات البنكية والبطاقات - العملات 

// check the observer
// check home.vue
// check trial days here and on stripe.com

// before delete plan  first check if there is any active subscription to this plan



// search for =>  check payment method for queries   <=  to make queries depends on payment method

// any extra items in tenantSubscription has to be moved to subscription model anc modify controllers for that



// start from class SubscriptionController   in Additional Configuration and Views




