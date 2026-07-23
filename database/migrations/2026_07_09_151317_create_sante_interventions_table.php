<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sante_interventions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lapin_id')->constrained('lapins')->cascadeOnDelete();
            $table->enum('type', ['maladie', 'traitement', 'vaccination', 'parasite_externe', 'autre']);
            $table->string('libelle');
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->enum('statut', ['en_cours', 'gueri', 'deces'])->default('en_cours');
            $table->text('traitement_applique')->nullable();
            $table->decimal('cout', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sante_interventions');
    }
};
