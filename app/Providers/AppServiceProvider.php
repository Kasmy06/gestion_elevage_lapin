<?php

namespace App\Providers;

use App\Models\Lapin;
use App\Models\MouvementAliment;
use App\Models\Parametre;
use App\Models\Pesee;
use App\Models\Sortie;
use App\Observers\LapinObserver;
use App\Observers\MouvementAlimentObserver;
use App\Observers\ParametreObserver;
use App\Observers\PeseeObserver;
use App\Observers\SortieObserver;
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
        Pesee::observe(PeseeObserver::class);
        Sortie::observe(SortieObserver::class);
        MouvementAliment::observe(MouvementAlimentObserver::class);
        Lapin::observe(LapinObserver::class);
        Parametre::observe(ParametreObserver::class);
    }
}
