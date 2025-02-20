<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Define the relationship with users
    public function users(){
        return $this->hasMany(User::class);
    }

    public function menuPermissions(){
    return $this->belongsToMany(Menu::class, 'role_menu_permissions')
                ->withPivot('view', 'add', 'edit', 'delete', 'assign', 'submit')
                ->withTimestamps();
    }
}

