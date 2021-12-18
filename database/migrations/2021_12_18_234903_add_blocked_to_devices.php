<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBlockedToDevices extends Migration {

    public function up(): void {
        Schema::table('devices', static function(Blueprint $table) {
            $table->boolean('blocked')->default(false)->after('ignore');
        });
    }

    public function down(): void {
        Schema::table('devices', static function(Blueprint $table) {
            $table->dropColumn(['blocked']);
        });
    }
}
