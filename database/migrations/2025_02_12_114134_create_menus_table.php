<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');  // Menu name
            $table->enum('type', ['main', 'sub', 'sub-sub']);  // Menu type
            $table->foreignId('parent_id')->nullable()->constrained('menus')->onDelete('cascade');  // Self-referencing parent_id
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('menus');
    }
};
