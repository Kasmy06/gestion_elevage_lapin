<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mouvements_aliment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aliment_id')->constrained('aliments')->cascadeOnDelete();
            $table->foreignId('cage_id')->nullable()->constrained('cages')->nullOnDelete();
            $table->date('date');
            $table->enum('type_mouvement', ['entree', 'distribution']);
            $table->decimal('quantite', 10, 2);
            $table->decimal('cout', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mouvements_aliment');
    }
};
