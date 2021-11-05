<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntranetDataTable extends Migration {

    public function up(): void {
        Schema::create('intranet_data', function(Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('scanDeviceId');

            $table->json('data');
            $table->boolean('processed')->default(false);

            $table->timestamps();

            $table->foreign('scanDeviceId')
                  ->references('id')
                  ->on('scan_devices');
        });
    }

    public function down(): void {
        Schema::dropIfExists('intranet_data');
    }
}
