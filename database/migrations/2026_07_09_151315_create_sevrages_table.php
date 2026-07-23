<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sevrages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mise_bas_id')->constrained('mises_bas')->cascadeOnDelete();
            $table->date('date_sevrage');
            $table->unsignedInteger('nb_sevres');
            $table->unsignedInteger('poids_moyen_g')->nullable();
            $table->boolean('lapereaux_generes')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sevrages');
    }
};
