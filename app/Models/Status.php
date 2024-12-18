<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $table = 'status';

    protected $fillable = [
        'name',
        'progress'
    ];

    public function columns()
    {
        return $this->hasMany(Column::class, 'status_id', 'id');
    }
}
