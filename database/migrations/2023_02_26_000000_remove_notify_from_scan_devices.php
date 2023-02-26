<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('scan_devices', static function(Blueprint $table) {
            $table->dropColumn(['notify']);
        });
    }

    public function down(): void {
        Schema::table('scan_devices', static function(Blueprint $table) {
            $table->boolean('notify')
                  ->default(0)
                  ->after('name');
        });
    }
};
