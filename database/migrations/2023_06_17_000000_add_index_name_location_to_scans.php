<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::table('scans', static function(Blueprint $table) {
            $table->index(['vehicle_name', 'modified_vehicle_name', 'latitude', 'longitude'], 'scans_index_name_location');
        });
    }

    public function down(): void {
        Schema::table('scans', static function(Blueprint $table) {
            $table->dropIndex('scans_index_name_location');
        });
    }
};
