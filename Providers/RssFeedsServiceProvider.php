<?php

namespace App\Modules\RssFeeds\Providers;

use App;
use Config;
use Lang;
use View;
use Illuminate\Support\ServiceProvider;

class RssFeedsServiceProvider extends ServiceProvider
{
    /**
     * Register the RssFeeds module service provider.
     *
     * @return void
     */
    public function register()
    {
        // This service provider is a convenient place to register your modules
        // services in the IoC container. If you wish, you may make additional
        // methods or service providers to keep the code more focused and granular.
        App::register('App\Modules\RssFeeds\Providers\RouteServiceProvider');

        $this->registerNamespaces();
    }

    /**
     * Register the TestsAndDemos module resource namespaces.
     *
     * @return void
     */
    protected function registerNamespaces()
    {
        Lang::addNamespace('rssfeeds', realpath(__DIR__.'/../Resources/Lang'));

        View::addNamespace('rssfeeds', base_path('resources/views/vendor/rssfeeds'));
        View::addNamespace('rssfeeds', realpath(__DIR__.'/../Resources/Views'));
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    { }

    /**
     * Register the Middleware
     *
     * @param  string $middleware
     */
    protected function addMiddleware($middleware)
    {
        $kernel = $this->app['Illuminate\Contracts\Http\Kernel'];
        $kernel->pushMiddleware($middleware);
    }
}
