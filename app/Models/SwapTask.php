<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SwapTask extends Model
{
    use HasFactory;

    protected $table = 'swap_task';

    protected $fillable = [
        'status',
        'old_user_id',
        'new_user_id',
        'card_id',
    ];

    public function oldUser()
    {
        return $this->belongsTo(User::class, 'old_user_id', 'id');
    }

    public function newUser()
    {
        return $this->belongsTo(User::class, 'new_user_id', 'id');
    }

    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id', 'id');
    }
}
