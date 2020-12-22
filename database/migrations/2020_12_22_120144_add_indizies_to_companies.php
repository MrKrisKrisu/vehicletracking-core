<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndiziesToCompanies extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('companies', function(Blueprint $table) {
            $table->string('name')->change();
        });
        Schema::table('companies', function(Blueprint $table) {
            $table->index(['name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('companies', function(Blueprint $table) {
            $table->dropIndex(['name']);
        });
        Schema::table('companies', function(Blueprint $table) {
            $table->text('name')->change();
        });
    }
}
