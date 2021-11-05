<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUicToVehicles extends Migration {

    public function up(): void {
        Schema::table('vehicles', function(Blueprint $table) {
            $table->string('uic_operator_id', 10)->nullable()->after('type');
            $table->unsignedTinyInteger('uic_check_number')->nullable()->after('type');
            $table->unsignedSmallInteger('uic_order_number')->nullable()->after('type');
            $table->unsignedSmallInteger('uic_series_number')->nullable()->after('type');
            $table->unsignedTinyInteger('uic_country_code')->nullable()->after('type');
            $table->unsignedTinyInteger('uic_type_code')->nullable()->after('type');
        });
    }

    public function down(): void {
        Schema::table('vehicles', function(Blueprint $table) {
            $table->dropColumn([
                                   'uic_type_code', 'uic_country_code', 'uic_series_number',
                                   'uic_order_number', 'uic_check_number', 'uic_operator_id',
                               ]);
        });
    }
}
