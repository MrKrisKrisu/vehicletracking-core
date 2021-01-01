<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveIndizesFromScans extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('scans', function(Blueprint $table) {
            $table->dropIndex(['latitude']);
            $table->dropIndex(['longitude']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('scans', function(Blueprint $table) {
            $table->index('latitude');
            $table->index('longitude');
        });
    }
}
