<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
