<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Middleware\AuthenticateJWT;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

class MiddlewareServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $router = $this->app['router'];

        // Register route-specific middleware here
        $router->aliasMiddleware('sanctum', EnsureFrontendRequestsAreStateful::class);
        $router->aliasMiddleware('auth.jwt', AuthenticateJWT::class);
    }
}
