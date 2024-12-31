<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\WorkspaceMembers;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function userRoles()
    {
        return $this->hasMany(UserRole::class, 'user_id', 'id');
    }

    public function roles()
    {
    return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function workspace()
    {
        return $this->hasMany(WorkspaceMembers::class, 'user_id', 'id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function workspaces()
    {
        return $this->hasMany(Workspace::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
