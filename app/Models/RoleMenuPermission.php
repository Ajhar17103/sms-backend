<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleMenuPermission extends Model
{
    use HasFactory;

    // Define the table name (optional if you follow convention)
    protected $table = 'role_menu_permissions';

    // Define the fillable attributes
    protected $fillable = [
        'role_id',
        'menu_id',
        'view',
        'add',
        'edit',
        'delete',
        'assign',
        'submit',
    ];

    // Define the relationships
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
