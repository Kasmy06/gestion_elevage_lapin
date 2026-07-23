<?php

use App\Livewire\Activites\Index as ActivitesIndex;
use App\Livewire\Alimentation\Aliments\Corbeille as AlimentCorbeille;
use App\Livewire\Alimentation\Aliments\Index as AlimentsIndex;
use App\Livewire\Alimentation\Mouvements\Index as MouvementsIndex;
use App\Livewire\Cages\Corbeille as CageCorbeille;
use App\Livewire\Cages\Index as CagesIndex;
use App\Livewire\Clients\Corbeille as ClientCorbeille;
use App\Livewire\Clients\Index as ClientsIndex;
use App\Livewire\Comptabilite\Corbeille as DepenseCorbeille;
use App\Livewire\Comptabilite\Index as ComptabiliteIndex;
use App\Livewire\Dashboard;
use App\Livewire\Employes\Corbeille as EmployeCorbeille;
use App\Livewire\Employes\Index as EmployesIndex;
use App\Livewire\Lapins\Corbeille as LapinCorbeille;
use App\Livewire\Lapins\Form as LapinForm;
use App\Livewire\Lapins\Index as LapinsIndex;
use App\Livewire\Lapins\Show as LapinShow;
use App\Livewire\Parametres\Index as ParametresIndex;
use App\Livewire\Races\Corbeille as RaceCorbeille;
use App\Livewire\Races\Index as RacesIndex;
use App\Livewire\Rapports\Index as RapportsIndex;
use App\Livewire\Reproduction\MisesBas\Index as MisesBasIndex;
use App\Livewire\Reproduction\Saillies\Index as SailliesIndex;
use App\Livewire\Sante\Index as SanteIndex;
use App\Livewire\Sorties\Index as SortiesIndex;
use App\Livewire\Utilisateurs\Index as UtilisateursIndex;
use App\Livewire\Ventes\Corbeille as VenteCorbeille;
use App\Livewire\Ventes\Index as VentesIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->to(auth()->check() ? route('dashboard') : route('login'));
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');

    Route::prefix('lapins')->name('lapins.')->group(function () {
        Route::get('/', LapinsIndex::class)->name('index');
        Route::get('/creer', LapinForm::class)->name('create');
        Route::get('/corbeille', LapinCorbeille::class)->name('corbeille')->middleware('admin');
        Route::get('/{lapin}/modifier', LapinForm::class)->name('edit');
        Route::get('/{lapin}', LapinShow::class)->name('show');
    });

    Route::prefix('reproduction')->name('reproduction.')->group(function () {
        Route::get('saillies', SailliesIndex::class)->name('saillies.index');
        Route::get('mises-bas', MisesBasIndex::class)->name('mises-bas.index');
    });

    Route::get('clapiers', CagesIndex::class)->name('cages.index');
    Route::get('races', RacesIndex::class)->name('races.index');
    Route::get('sante', SanteIndex::class)->name('sante.index');

    Route::prefix('alimentation')->name('alimentation.')->group(function () {
        Route::get('aliments', AlimentsIndex::class)->name('aliments.index');
        Route::get('aliments/corbeille', AlimentCorbeille::class)->name('aliments.corbeille')->middleware('admin');
        Route::get('mouvements', MouvementsIndex::class)->name('mouvements.index');
    });

    Route::get('sorties', SortiesIndex::class)->name('sorties.index');
    Route::get('clients', ClientsIndex::class)->name('clients.index');
    Route::get('ventes', VentesIndex::class)->name('ventes.index');
    Route::get('comptabilite', ComptabiliteIndex::class)->name('comptabilite.index');
    Route::get('rapports', RapportsIndex::class)->name('rapports.index');

    Route::middleware('admin')->group(function () {
        Route::get('parametres', ParametresIndex::class)->name('parametres.index');
        Route::get('utilisateurs', UtilisateursIndex::class)->name('utilisateurs.index');
        Route::get('employes', EmployesIndex::class)->name('employes.index');
        Route::get('employes/corbeille', EmployeCorbeille::class)->name('employes.corbeille');
        Route::get('clients/corbeille', ClientCorbeille::class)->name('clients.corbeille');
        Route::get('ventes/corbeille', VenteCorbeille::class)->name('ventes.corbeille');
        Route::get('comptabilite/depenses/corbeille', DepenseCorbeille::class)->name('comptabilite.depenses.corbeille');
        Route::get('clapiers/corbeille', CageCorbeille::class)->name('cages.corbeille');
        Route::get('races/corbeille', RaceCorbeille::class)->name('races.corbeille');
        Route::get('activites', ActivitesIndex::class)->name('activites.index');
    });

    Route::view('profile', 'profile')->name('profile');
});

require __DIR__.'/auth.php';
