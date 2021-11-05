<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSpeedAndConnectivityToScans extends Migration {

    public function up(): void {
        Schema::table('scans', function(Blueprint $table) {
            $table->string('connectivity_state')->nullable()->after('hidden');
            $table->unsignedSmallInteger('speed')->nullable()->after('hidden');
        });
    }

    public function down(): void {
        Schema::table('scans', function(Blueprint $table) {
            $table->dropColumn(['connectivity_state', 'speed']);
        });
    }
}
