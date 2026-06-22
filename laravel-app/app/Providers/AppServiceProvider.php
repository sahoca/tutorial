<?php

namespace App\Providers;

use App\Models\MenuItem;
use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Tüm site (frontend) view'lerine menü ve ayarları paylaş.
        View::composer('site.*', function ($view) {
            $settings = Schema::hasTable('settings')
                ? Setting::pluck('value', 'key')->toArray()
                : [];

            $menus = ['header' => collect(), 'footer' => collect()];
            if (Schema::hasTable('menu_items')) {
                $menus = MenuItem::where('is_active', true)
                    ->whereNull('parent_id')
                    ->orderBy('sort_order')
                    ->get()
                    ->groupBy('location');
            }

            $view->with('siteSettings', $settings)
                ->with('headerMenu', $menus['header'] ?? collect())
                ->with('footerMenu', $menus['footer'] ?? collect());
        });
    }
}
