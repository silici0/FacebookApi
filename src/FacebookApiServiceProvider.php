<?php

namespace silici0\FacebookApi;

use Illuminate\Support\ServiceProvider;

class FacebookApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/facebook-api.php', 'facebook-api');

        $this->app->bind('facebookapi', function($app) {
            $config = $app['config']->get('facebook-api.facebook_config');

            return new FacebookApi($app['config']);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/config/facebook-api.php' => \config_path('facebook-api.php'),
            ], 'config');
        }
    }
}
