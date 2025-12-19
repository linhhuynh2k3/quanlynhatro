<?php

namespace App\Providers;

use App\Models\Category;
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
        View::composer('layouts.frontend', function ($view) {
            $categories = Category::with(['children' => function ($query) {
                $query->where('is_active', true)
                    ->orderBy('position')
                    ->orderBy('name');
            }])
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->orderBy('position')
                ->orderBy('name')
                ->get();

            $view->with('headerCategories', $categories);
        });
    }
}
