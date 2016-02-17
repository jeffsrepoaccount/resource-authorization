<?php namespace Jeffsrepoaccount\ResourceAuthorization;

use Illuminate\Support\ServiceProvider as BaseProvider;
use League\OAuth2\Server\ResourceServer;

class ServiceProvider extends BaseProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        die('still got vendor service provider!!!!');
        //
        $app = $this->app;

        $app->bind('League\OAuth2\Server\ResourceServer', function() use($app) {
            $server = new ResourceServer(
                $app->make('LucaDegasperi\OAuth2Server\Storage\FluentSession'),
                $app->make('LucaDegasperi\OAuth2Server\Storage\FluentAccessToken'),
                $app->make('LucaDegasperi\OAuth2Server\Storage\FluentClient'),
                $app->make('LucaDegasperi\OAuth2Server\Storage\FluentScope')
            );

            return $server;
        });
    }
}
