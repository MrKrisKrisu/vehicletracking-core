<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDatatypesOfScans extends Migration
{
    /**
     * Run the migrations.
     *
     * SELECT *,LENGTH(bssid) FROM `scans` WHERE 1 ORDER BY `LENGTH(bssid)` DESC
     *
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scans', function (Blueprint $table) {
            $table->string('bssid', 17)->change();
            $table->string('ssid')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scans', function (Blueprint $table) {
            $table->text('bssid')->change();
            $table->text('ssid')->change();
        });
    }
}
