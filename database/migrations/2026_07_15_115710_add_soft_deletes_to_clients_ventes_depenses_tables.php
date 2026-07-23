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
        Schema::table('clients', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('ventes', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('depenses', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('ventes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('depenses', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
