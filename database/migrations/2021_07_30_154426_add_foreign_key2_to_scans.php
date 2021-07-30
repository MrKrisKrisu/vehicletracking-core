<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKey2ToScans extends Migration {

    public function up(): void {
        Schema::table('scans', function(Blueprint $table) {
            $table->foreign('bssid')
                  ->references('bssid')
                  ->on('devices');
        });
    }

    public function down(): void {
        Schema::table('scans', function(Blueprint $table) {
            $table->dropForeign(['bssid']);
        });
    }
}
