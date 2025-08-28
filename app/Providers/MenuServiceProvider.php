<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        view()->composer('*', function ($view) {
            $verticalMenuData = null;
            $horizontalMenuData = null;

            if (Auth::guard('web')->check()) {
                $adminMenuPath = base_path('resources/menu/verticalMenuAdmin.json');
                if (File::exists($adminMenuPath)) {
                    $verticalMenuJson = File::get($adminMenuPath);
                    $verticalMenuData = json_decode($verticalMenuJson);
                }
            } elseif (Auth::guard('rider')->check()) {
                $riderMenuPath = base_path('resources/menu/verticalMenuRider.json');
                if (File::exists($riderMenuPath)) {
                    $verticalMenuJson = File::get($riderMenuPath);
                    $verticalMenuData = json_decode($verticalMenuJson);
                }
            }

            $view->with('menuData', [$verticalMenuData, $horizontalMenuData]);
        });
    }
}
