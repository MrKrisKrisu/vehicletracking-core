<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Scans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scans', function (Blueprint $table) {
            $table->id('id');
            $table->text('vehicle_name')->nullable();
            $table->text('bssid');
            $table->text('ssid');
            $table->text('signal');
            $table->text('quality');
            $table->text('frequency');
            $table->text('bitrates');
            $table->text('encrypted');
            $table->text('channel');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scans');
    }
}
