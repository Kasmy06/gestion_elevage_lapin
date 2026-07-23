<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sorties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lapin_id')->constrained('lapins')->cascadeOnDelete();
            $table->enum('type', ['vente', 'abattage', 'mort', 'don']);
            $table->date('date');
            $table->unsignedInteger('poids_g')->nullable();
            $table->decimal('prix', 10, 2)->nullable();
            $table->string('acheteur')->nullable();
            $table->string('cause')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sorties');
    }
};
