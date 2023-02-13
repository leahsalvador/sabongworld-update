<?php

namespace App\Models;

use App\Http\Livewire\Betting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Winner extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $table = 'winner_notify';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'game_rounds_id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
//    protected $dateFormat = 'U';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'game_rounds_id',
        'winner',
        'amount',
        'player_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the user for the winning bet.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'player_id', 'id');
    }

    /**
     * Get the user for the winning bet.
     */
    public function games()
    {
        return $this->hasMany(GameRound::class, 'game_rounds_id', 'id');
    }
}
