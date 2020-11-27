<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRowsToScanDevices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scan_devices', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')
                  ->nullable()
                  ->default(null)
                  ->index()
                  ->after('id');

            $table->timestamp('valid_until')
                  ->default(null)
                  ->nullable()
                  ->after('name');

            $table->decimal('longitude', 9, 6)
                  ->nullable()
                  ->default(null)
                  ->after('name');
            $table->decimal('latitude', 9, 6)
                  ->nullable()
                  ->default(null)
                  ->after('name');


            $table->foreign('user_id')
                  ->references('id')
                  ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scan_devices', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
            $table->dropColumn('valid_until');
        });
    }
}
