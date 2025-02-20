<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleMenuPermissionsTable extends Migration{
    public function up(){
        Schema::create('role_menu_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->onDelete('cascade');  // Reference to roles
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');  // Reference to menus
            $table->boolean('view')->default(false);
            $table->boolean('add')->default(false);
            $table->boolean('edit')->default(false);
            $table->boolean('delete')->default(false);
            $table->boolean('assign')->default(false);
            $table->boolean('submit')->default(false);
            $table->timestamps();
        });
    }

    public function down(){
        Schema::dropIfExists('role_menu_permissions');
    }
};

