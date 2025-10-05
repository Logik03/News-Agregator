<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Providers\NewsApiProvider;
use App\Services\Providers\GuardianProvider;
use App\Services\Providers\NytProvider;

class NewsProvidersServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('news.provider.newsapi', function($app){
            return new NewsApiProvider(env('NEWSAPIORG_KEY'));
        });

        $this->app->singleton('news.provider.guardian', function($app){
            return new GuardianProvider(env('GUARDIAN_KEY'));
        });

        $this->app->singleton('news.provider.nyt', function($app){
            return new NytProvider(env('NEWYORKTIMES_KEY'));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
