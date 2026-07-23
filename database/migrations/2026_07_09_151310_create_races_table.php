<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('races', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();
            $table->enum('categorie', ['naine', 'legere', 'moyenne', 'lourde']);
            $table->decimal('poids_min_kg', 4, 2)->nullable();
            $table->decimal('poids_max_kg', 4, 2)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('races');
    }
};
