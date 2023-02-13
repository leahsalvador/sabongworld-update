<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameRound extends Model
{
    use HasFactory;
    protected $fillable = ['status'];

    public function bettingHistory()
    {
        return $this->hasMany(BettingHistory::class, 'game_rounds_id');
    }
}
