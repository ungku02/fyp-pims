<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    
    protected $table = 'card';

    protected $fillable = [
        'column_id',
        'status_id',
        'title',
        'description'
    ];

    public function columns()
    {
    return $this->belongsTo(Column::class, 'column_id', 'id');

    }
}
