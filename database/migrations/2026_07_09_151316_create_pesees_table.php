<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lapin_id')->constrained('lapins')->cascadeOnDelete();
            $table->date('date_pesee');
            $table->unsignedInteger('poids_g');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesees');
    }
};
