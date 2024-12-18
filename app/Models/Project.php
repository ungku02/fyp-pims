<?php

namespace App\Models;

use App\Models\Column;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $table = 'project';

    protected $fillable = [
        'title',
        'description',
        'workspace_id',
        'image'
    ];

    public function workspace()
    {
    return $this->belongsTo(Workspace::class, 'workspace_id', 'id');

    }

    public function column()
    {
    return $this->hasMany(Column::class, 'project_id', 'id');

    }

    public function userRole()
    {
        return $this->hasMany(UserRole::class, 'project_id', 'id');
    }
}
