<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interface\IPontoUsuarioRepository;
use App\Repository\PontoUsuarioRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind(IPontoUsuarioRepository::class, PontoUsuarioRepository::class);
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
