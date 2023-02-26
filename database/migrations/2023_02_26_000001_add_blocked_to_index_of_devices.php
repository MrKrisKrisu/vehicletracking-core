<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::table('devices', static function(Blueprint $table) {
            $table->index(['blocked']);
        });
    }
    public function down(): void {
        Schema::table('devices', static function(Blueprint $table) {
            $table->dropIndex(['blocked']);
        });
    }
};
