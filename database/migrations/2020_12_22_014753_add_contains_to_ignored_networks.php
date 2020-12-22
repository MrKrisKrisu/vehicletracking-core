<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContainsToIgnoredNetworks extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('ignored_networks', function(Blueprint $table) {
            $table->boolean('contains')
                  ->index()
                  ->default(0)
                  ->after('ssid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('ignored_networks', function(Blueprint $table) {
            $table->dropColumn('contains');
        });
    }
}
