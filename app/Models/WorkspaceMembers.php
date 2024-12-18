<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkspaceMembers extends Model
{
    use HasFactory;
    
    protected $table = 'workspace_members';

    protected $fillable = [
        'user_id',
        'workspace_id',
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function workspaces()
    {
        return $this->belongsTo(Workspace::class, 'workspace_id', 'id');
    }

}
