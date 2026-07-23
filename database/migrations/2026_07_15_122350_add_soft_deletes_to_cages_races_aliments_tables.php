<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cages', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('races', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('aliments', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cages', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('races', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('aliments', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
