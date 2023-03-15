<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('devices', function(Blueprint $table) {
            $table->foreign('vehicle_id')
                  ->references('id')
                  ->on('vehicles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('devices', function(Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
        });
    }
};
