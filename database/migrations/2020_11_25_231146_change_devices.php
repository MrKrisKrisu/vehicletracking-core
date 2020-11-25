<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeDevices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->string('bssid', 17)->change();
            $table->unsignedBigInteger('vehicle_id')->change();

            $table->timestamps();
        });

        DB::table('devices')->update([
                                         'created_at' => DB::raw('firstSeen'),
                                         'updated_at' => DB::raw('lastSeen'),
                                     ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->string('bssid', 34)->change();
            $table->integer('vehicle_id')->change();

            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
}
