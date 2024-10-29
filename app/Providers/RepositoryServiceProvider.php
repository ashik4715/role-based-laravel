<?php

namespace App\Providers;

use App\Interfaces\ConfigurationRepositoryInterface;
use App\Repositories\Application\ApplicationRepository;
use App\Repositories\Application\ApplicationRepositoryInterface;
use App\Repositories\ApplicationLog\ApplicationLogRepository;
use App\Repositories\ApplicationLog\ApplicationLogRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Repositories\ConfigurationRepository;
use App\Repositories\EloquentRepositoryInterface;
use App\Repositories\Page\PageRepository;
use App\Repositories\Page\PageRepositoryInterface;
use App\Repositories\Section\SectionRepository;
use App\Repositories\Section\SectionRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use League\Config\ConfigurationInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(ConfigurationRepositoryInterface::class, ConfigurationRepository::class);
        $this->app->bind(ApplicationRepositoryInterface::class, ApplicationRepository::class);
        $this->app->bind(ApplicationLogRepositoryInterface::class, ApplicationLogRepository::class);
        $this->app->bind(ConfigurationInterface::class, ConfigurationRepository::class);
        $this->app->bind(EloquentRepositoryInterface::class, BaseRepository::class);
        $this->app->bind(PageRepositoryInterface::class, PageRepository::class);
        $this->app->bind(SectionRepositoryInterface::class, SectionRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
