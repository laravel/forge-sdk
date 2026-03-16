<?php

declare(strict_types=1);

namespace Laravel\Forge;

use Illuminate\Support\ServiceProvider;

class ForgeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ForgeManager::class, function ($app) {
            return new ForgeManager($app['config']->get('services.forge.token'));
        });
    }
}
