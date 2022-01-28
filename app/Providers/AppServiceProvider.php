<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use App\Detail;
use App\User;
use App\Renewal;

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
        Schema::defaultStringLength(191);
        $details = Detail::all()->count();
        $userRenewals = Renewal::all()->count();
        $users = User::where('role_id', 2)->count();
        $admins = User::where('role_id', 1)->count();
        View::share('totalDetails', $details);
        View::share('totalNormalUsers', $users);
        View::share('totalAdminUsers', $admins);
        View::share('userRenewals', $userRenewals);
    }
}
