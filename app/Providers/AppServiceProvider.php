<?php

namespace App\Providers;

use App\Repositories\TicketRepository;
use App\Repositories\Interfaces\TicketRepositoryInterface;
use App\Repositories\Master\CategoryRepository;
use App\Repositories\Master\Contracts\CategoryRepositoryInterface;
use App\Repositories\Master\PriorityRepository;
use App\Repositories\Master\Contracts\PriorityRepositoryInterface;
use App\Repositories\Master\StatusRepository;
use App\Repositories\Master\Contracts\StatusRepositoryInterface;
use App\Repositories\Master\SubCategoryRepository;
use App\Repositories\Master\Contracts\SubCategoryRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            TicketRepositoryInterface::class,
            TicketRepository::class
        );
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(PriorityRepositoryInterface::class, PriorityRepository::class);
        $this->app->bind(StatusRepositoryInterface::class, StatusRepository::class);
        $this->app->bind(SubCategoryRepositoryInterface::class, SubCategoryRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
