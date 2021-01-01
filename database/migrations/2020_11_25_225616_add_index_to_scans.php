<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToScans extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('scans', function(Blueprint $table) {
            $table->string('vehicle_name')->change();
        });
        Schema::table('scans', function(Blueprint $table) {
            $table->index('vehicle_name');
            $table->index('modified_vehicle_name');
            $table->index('bssid');
            $table->index('latitude');
            $table->index('longitude');
            $table->index('scanDeviceId');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('scans', function(Blueprint $table) {
            $table->dropIndex(['vehicle_name']);
            $table->dropIndex(['modified_vehicle_name']);
            $table->dropIndex(['bssid']);
            $table->dropIndex(['latitude']);
            $table->dropIndex(['longitude']);
            $table->dropIndex(['scanDeviceId']);
            $table->dropIndex(['created_at']);
        });
    }
}
