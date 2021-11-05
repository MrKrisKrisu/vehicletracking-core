<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToVehicles extends Migration {

    public function up(): void {
        Schema::table('vehicles', function(Blueprint $table) {
            $table->foreign('uic_type_code')->references('id')->on('uic_types');
            $table->foreign('uic_country_code')->references('id')->on('uic_countries');
            $table->foreign('uic_series_number')->references('id')->on('uic_series');
        });
    }

    public function down(): void {
        Schema::table('vehicles', function(Blueprint $table) {
            $table->dropForeign(['uic_type_code']);
            $table->dropForeign(['uic_country_code']);
            $table->dropForeign(['uic_series_number']);
        });
    }
}
