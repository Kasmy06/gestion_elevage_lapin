<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mortalites_lapereaux', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mise_bas_id')->constrained('mises_bas')->cascadeOnDelete();
            $table->date('date');
            $table->string('stade');
            $table->unsignedInteger('nombre');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mortalites_lapereaux');
    }
};
