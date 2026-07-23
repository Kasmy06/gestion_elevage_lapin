<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->string('user_name')->nullable()->after('user_id');
            $table->string('user_email')->nullable()->after('user_name');
        });

        // Fige le nom/email des utilisateurs encore présents sur les entrées existantes,
        // pour que l'historique reste attribué même si le compte est supprimé plus tard.
        DB::statement('
            UPDATE activity_logs
            SET user_name = (SELECT name FROM users WHERE users.id = activity_logs.user_id),
                user_email = (SELECT email FROM users WHERE users.id = activity_logs.user_id)
            WHERE user_id IS NOT NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn(['user_name', 'user_email']);
        });
    }
};
