<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use App\Services\Application\DraftChecker;
use App\Services\wegro\BasicCheckerForWegro;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

        $this->app->register(RepositoryServiceProvider::class);
        $this->app->bind(DraftChecker::class, BasicCheckerForWegro::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Model::shouldBeStrict(!$this->app->isProduction());
        Model::shouldBeStrict(false);

        if (env('REDIRECT_HTTPS')) {
            URL::forceScheme('https');
        }
    }
}
