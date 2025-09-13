<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;


class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
                // $settings = Setting::select(['id' ,'attendance_method' , 'logo'])->first();

        return [
            ...parent::share($request), 
            'ziggy' => fn() => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'vital_error' => $request->session()->get('vital_error'),

            ],

            // 'logo' => asset('attachments/logo/' . $settings?->logo) ?? '',
            'logo' => null,

            'csrf_token' => csrf_token(),

            'locale' => App::getLocale(),
            // 'paginationNumber' => session('paginationNumber'),
            'paginationNumber' => PAGINATION_COUNT,


            'year' => date('Y'),

            'menus' => [
                [
                    'label' => 'Dashboard',
                    'url' => route('dashboard'),
                    'isActive' => $request->routeIs('dashboard*'),
                    'isVisible' => true,
                    // 'isVisible' => $request->user()?->can('view dashboard'),
                    'hasSubmenu' => false,
                ],

            ],
        ];
    }
}
