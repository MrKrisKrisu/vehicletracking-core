<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotificationToScanDevices extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('scan_devices', function(Blueprint $table) {
            $table->boolean('notify')
                  ->default(0)
                  ->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('scan_devices', function(Blueprint $table) {
            $table->dropColumn('notify');
        });
    }
}
