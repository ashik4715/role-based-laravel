<?php

namespace App\Repositories\Providers;

use App\Repositories\ConfigurationRepository;
use Illuminate\Support\ServiceProvider;
use League\Config\ConfigurationInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ConfigurationInterface::class, ConfigurationRepository::class);
    }
}
