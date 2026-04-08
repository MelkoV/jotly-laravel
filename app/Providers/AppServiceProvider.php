<?php

namespace App\Providers;

use App\Contracts\Repositories\ListRepositoryContract;
use App\Contracts\Repositories\UserRepositoryContract;
use App\Contracts\Services\JwtServiceContract;
use App\Contracts\Services\ListServiceContract;
use App\Contracts\Services\UserServiceContract;
use App\Repositories\ListRepository;
use App\Repositories\UserRepository;
use App\Services\JwtService;
use App\Services\ListService;
use App\Services\SwaggerService as CustomSwaggerService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;
use RonasIT\AutoDoc\Services\SwaggerService as BaseSwaggerService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(JwtServiceContract::class, JwtService::class);
        $this->app->bind(UserServiceContract::class, UserService::class);
        $this->app->bind(ListServiceContract::class, ListService::class);

        $this->app->bind(UserRepositoryContract::class, UserRepository::class);
        $this->app->bind(ListRepositoryContract::class, ListRepository::class);
        $this->app->bind(BaseSwaggerService::class, CustomSwaggerService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
