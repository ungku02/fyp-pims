<?php

namespace App\Models;

use App\Models\Card;
use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Column extends Model
{
    use HasFactory;

    protected $table = 'column';

    protected $fillable = [
        'name',
        'position',
        'project_id',
        'status_id'
    ];

    public function project()
    {
    return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function cards()
    {
    return $this->hasMany(Card::class);
    }

}
