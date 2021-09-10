<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeScanSsidNullableAtScans extends Migration {

    public function up(): void {
        Schema::table('scans', function(Blueprint $table) {
            $table->string('ssid')
                  ->default(null)
                  ->nullable()
                  ->change();
        });
    }

    public function down(): void {
        Schema::table('scans', function(Blueprint $table) {
            $table->string('ssid')
                  ->change();
        });
    }
}
