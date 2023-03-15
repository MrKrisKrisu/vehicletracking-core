<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('scans', function(Blueprint $table) {
            $table->foreign('bssid')
                  ->references('bssid')
                  ->on('devices');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('scans', function(Blueprint $table) {
            $table->dropForeign(['bssid']);
        });
    }
};
