<?php

namespace App\Models;

use App\Models\WorkspaceMembers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Workspace extends Model
{
    use HasFactory;

    protected $table = 'workspace';

    protected $fillable = [
        'title',
        'description',
        'user_id'
    ];

    public function project()
    {
    return $this->hasMany(Project::class, 'workspace_id', 'id');

    }

    public function members()
    {
        return $this->hasMany(WorkspaceMembers::class, 'workspace_id', 'id')->with('users');
    }
}
