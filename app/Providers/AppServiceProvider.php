<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('complex', function($attribute, $value, $parameters, $validator) {
            $hasUppercase = preg_match("/[A-Z]/", $value);
            $hasLowercase = preg_match("/[a-z]/", $value);
            $hasNumbers = preg_match("/\d/", $value);
            $hasSymbols = preg_match("/\W/", $value);

            return !! ( $hasUppercase + $hasLowercase + $hasNumbers + $hasSymbols > 2 );
        });

        Validator::extend('master_password', function($attribute, $value, $parameters, $validator) {
            return $value == config('app.master_password');
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
