<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cages', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->string('emplacement')->nullable();
            $table->enum('type', ['individuelle', 'maternite', 'engraissement', 'quarantaine'])->default('individuelle');
            $table->unsignedInteger('capacite')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cages');
    }
};
