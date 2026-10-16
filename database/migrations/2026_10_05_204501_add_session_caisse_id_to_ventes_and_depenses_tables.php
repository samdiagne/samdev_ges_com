<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->foreignId('session_caisse_id')->nullable()->after('user_id')->constrained('sessions_caisse')->nullOnDelete();
        });

        Schema::table('depenses', function (Blueprint $table) {
            $table->foreignId('session_caisse_id')->nullable()->after('user_id')->constrained('sessions_caisse')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->dropForeign(['session_caisse_id']);
            $table->dropColumn('session_caisse_id');
        });

        Schema::table('depenses', function (Blueprint $table) {
            $table->dropForeign(['session_caisse_id']);
            $table->dropColumn('session_caisse_id');
        });
    }
};
