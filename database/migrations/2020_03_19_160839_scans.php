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
            $table->integer('signal')->nullable();
            $table->text('quality')->nullable();
            $table->text('frequency')->nullable();
            $table->text('bitrates')->nullable();
            $table->text('encrypted')->nullable();
            $table->integer('channel')->nullable();
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
