<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interface\IPontoUsuarioRepository;
use App\Interface\IMunicipioRepository;
use App\Repository\PontoUsuarioRepository;
use App\Repository\MunicipioRepository;

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
        $this->app->bind(IMunicipioRepository::class, MunicipioRepository::class);
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
