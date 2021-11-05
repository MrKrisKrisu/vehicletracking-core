<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUicCountriesTable extends Migration {

    public function up(): void {
        Schema::create('uic_countries', function(Blueprint $table) {
            $table->unsignedTinyInteger('id')->autoIncrement();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('uic_countries');
    }
}
