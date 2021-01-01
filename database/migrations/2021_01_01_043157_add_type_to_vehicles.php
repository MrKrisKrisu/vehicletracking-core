<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToVehicles extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('vehicles', function(Blueprint $table) {
            $table->enum('type', ['bus', 'tram', 'train'])
                  ->nullable()
                  ->default(null)
                  ->after('vehicle_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('vehicles', function(Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
