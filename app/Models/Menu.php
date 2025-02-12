<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['name', 'type', 'parent_id'];

    // Relationship: A menu can have submenus
    public function subMenus()
    {
        return $this->hasMany(Menu::class, 'parent_id');
    }
}
