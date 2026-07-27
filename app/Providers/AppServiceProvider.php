<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // When running behind a reverse proxy (ngrok, etc.),
        // trust all proxies so Laravel reads the correct scheme/host.
        if ($this->app->runningInConsole()) {
            return;
        }

        $request = request();

        // If the request came through a proxy (ngrok), force HTTPS
        // and set the root URL dynamically from the forwarded host.
        $forwardedProto = $request->header('X-Forwarded-Proto');
        $forwardedHost  = $request->header('X-Forwarded-Host');

        if ($forwardedProto === 'https' || $request->isSecure()) {
            URL::forceScheme('https');
        }

        // When accessed through a proxy like ngrok, override the root URL
        // so url(), route(), and asset() generate correct external URLs.
        if ($forwardedHost) {
            $scheme = $forwardedProto ?: ($request->isSecure() ? 'https' : 'http');
            URL::forceRootUrl("{$scheme}://{$forwardedHost}");
        }
    }
}
