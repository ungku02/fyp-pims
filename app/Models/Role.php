<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'role';

    protected $fillable = [
        'name',
    ];

    public function userRoles()
    {
        return $this->hasMany(UserRole::class, 'role_id', 'id');
    }

     public function user()
     {
     return $this->hasMany(User::class);
     }

    public function isOwner($userId)
    {
        return $this->workspace->user_id === $userId;
    }
}
