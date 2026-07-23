<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database avec les données de référence
     * (races, clapiers, aliments, compte administrateur).
     *
     * Pour explorer l'application avec un cheptel de démonstration,
     * lancez ensuite : php artisan db:seed --class=DemoSeeder
     */
    public function run(): void
    {
        $this->call([
            RaceSeeder::class,
            CageSeeder::class,
            AlimentSeeder::class,
            UserSeeder::class,
        ]);
    }
}
