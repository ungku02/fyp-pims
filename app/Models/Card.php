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
        'description',
        'due_date',
        'urgency',
        'attachment',
        'user_project_id',
    ];

    protected $casts = [
        'due_date' => 'datetime:d M Y',
    ];

    public function columns()
    {
        return $this->belongsTo(Column::class, 'column_id', 'id');
    }

    public function userRole()
    {
        return $this->belongsTo(UserRole::class, 'user_project_id', 'user_id');
    }

    public function swapTasks()
    {
        return $this->hasMany(SwapTask::class, 'card_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_project_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id', 'id');
    }

    // Add this method to get the status name
    public function getStatusNameAttribute()
    {
        return $this->status ? $this->status->name : 'No status';
    }
}
