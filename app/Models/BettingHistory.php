<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BettingHistory extends Model
{
    use HasFactory;

    public function gameRounds()
    {
        return $this->belongsTo(GameRounds::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function player()
    {
        return $this->hasOne(User::class,'id','player_id');
    }
}
