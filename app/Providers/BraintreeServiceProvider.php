<?php

namespace App\Providers;

use Braintree\Configuration;
use Illuminate\Support\ServiceProvider;

class BraintreeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
//        dd($this->app['config']);
        //
        Configuration::environment($this->app['config']->get('services.braintree.environment'));
        Configuration::merchantId($this->app['config']->get('services.braintree.merchant_id'));
        Configuration::publicKey($this->app['config']->get('services.braintree.public_key'));
        Configuration::privateKey($this->app['config']->get('services.braintree.private_key'));
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
