<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeviceAuthorization extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scan_devices', function (Blueprint $table) {
            $table->id();
            $table->uuid('token')->unique();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::table('scans', function (Blueprint $table) {
            $table->string('scanDeviceId')
                ->nullable()
                ->after('channel')
                ->references('id')->on('scan_devices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scan_devices');

        Schema::table('scans', function (Blueprint $table) {
            $table->dropColumn('scanDeviceId');
        });
    }
}
