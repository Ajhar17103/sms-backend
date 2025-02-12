<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('property_name');
            $table->string('address');
            $table->decimal('cost_per_night', 8, 2);
            $table->integer('available_rooms');
            $table->string('property_image')->nullable();
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('hotels');
    }
};
