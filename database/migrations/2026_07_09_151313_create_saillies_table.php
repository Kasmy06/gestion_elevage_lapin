<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saillies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('male_id')->constrained('lapins')->cascadeOnDelete();
            $table->foreignId('femelle_id')->constrained('lapins')->cascadeOnDelete();
            $table->date('date_saillie');
            $table->enum('diagnostic_gestation', ['en_attente', 'positif', 'negatif'])->default('en_attente');
            $table->date('date_diagnostic')->nullable();
            $table->date('date_mise_bas_prevue');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saillies');
    }
};
