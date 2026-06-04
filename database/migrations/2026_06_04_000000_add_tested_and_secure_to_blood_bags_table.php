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
        Schema::table('blood_bags', function (Blueprint $table) {
            $table->boolean('is_tested')->default(false)->after('status');
            $table->boolean('is_secure')->default(false)->after('is_tested');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blood_bags', function (Blueprint $table) {
            $table->dropColumn(['is_tested', 'is_secure']);
        });
    }
};
