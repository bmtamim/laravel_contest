<?php

namespace App\Providers;

use App\Models\OrderItem;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //Load user sidebar data
        View::composer('frontend.layouts.partials.user-sidebar', function ($view) {
            $orderItem = OrderItem::query()->whereHas('order', function ($q) {
                $q->whereHas('customer', function ($q) {
                    $q->where('user_id', auth()->id());
                });
            })->whereHas('contest', function ($q) {
                $q->where('is_draw', false);
            })->count();

            $view->with(['uc' => $orderItem]);
        });


    }
}
