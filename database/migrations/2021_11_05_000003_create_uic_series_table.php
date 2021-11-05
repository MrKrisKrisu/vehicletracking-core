<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUicSeriesTable extends Migration {

    public function up(): void {
        Schema::create('uic_series', function(Blueprint $table) {
            $table->unsignedSmallInteger('id')->autoIncrement();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('uic_series');
    }
}
