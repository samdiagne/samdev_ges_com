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
        Schema::table('company_settings', function (Blueprint $table) {
            $table->string('facebook')->nullable()->after('whatsapp');
            $table->string('instagram')->nullable()->after('facebook');
            $table->string('tiktok')->nullable()->after('instagram');
        });
    }

    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->dropColumn(['facebook', 'instagram', 'tiktok']);
        });
    }
};
