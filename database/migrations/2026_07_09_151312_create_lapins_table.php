<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lapins', function (Blueprint $table) {
            $table->id();
            $table->string('identifiant')->unique()->comment('Numéro de boucle / identification');
            $table->foreignId('race_id')->nullable()->constrained('races')->nullOnDelete();
            $table->enum('sexe', ['male', 'femelle'])->nullable()->comment('Indéterminé pour un lapereau non encore sexé');
            $table->date('date_naissance')->nullable();
            $table->foreignId('pere_id')->nullable()->constrained('lapins')->nullOnDelete();
            $table->foreignId('mere_id')->nullable()->constrained('lapins')->nullOnDelete();
            $table->foreignId('cage_id')->nullable()->constrained('cages')->nullOnDelete();
            $table->enum('statut', ['jeune', 'reproducteur', 'engraissement', 'vendu', 'abattu', 'mort', 'donne'])->default('jeune');
            $table->enum('origine', ['naissance_elevage', 'achat'])->default('naissance_elevage');
            $table->unsignedInteger('poids_actuel_g')->nullable();
            $table->date('date_acquisition')->nullable();
            $table->string('photo_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lapins');
    }
};
