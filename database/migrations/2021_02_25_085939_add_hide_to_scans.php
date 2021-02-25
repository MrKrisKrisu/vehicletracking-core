<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHideToScans extends Migration {

    public function up(): void {
        Schema::table('scans', function(Blueprint $table) {
            $table->boolean('hidden')
                  ->default(0)
                  ->after('scanDeviceId');
        });
    }

    public function down(): void {
        Schema::table('scans', function(Blueprint $table) {
            $table->dropColumn('hidden');
        });
    }
}
