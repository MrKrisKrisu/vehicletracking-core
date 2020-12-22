<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndiziesToScans extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('scans', function(Blueprint $table) {
            $table->index(['bssid', 'created_at']);
            $table->index(['bssid', 'ssid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('scans', function(Blueprint $table) {
            $table->dropIndex(['bssid', 'created_at']);
            $table->dropIndex(['bssid', 'ssid']);
        });
    }
}
