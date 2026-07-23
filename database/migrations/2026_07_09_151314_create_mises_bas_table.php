<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mises_bas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saillie_id')->constrained('saillies')->cascadeOnDelete();
            $table->foreignId('femelle_id')->constrained('lapins')->cascadeOnDelete();
            $table->date('date_mise_bas');
            $table->unsignedInteger('nb_nes_vivants')->default(0);
            $table->unsignedInteger('nb_morts_nes')->default(0);
            $table->date('date_sevrage_prevue');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mises_bas');
    }
};
