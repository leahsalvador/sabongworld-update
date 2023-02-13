<?php

namespace App\Models;

use App\Scopes\BotFilter;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class User extends Authenticatable
{
    use HasFactory, Notifiable,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'users';

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone_number',
        'facebook_link',
        'password',
        'user_level',
        'status',
        'code',
        'referral_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        //static::addGlobalScope(new BotFilter);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
    public function bettingHistory()
    {
        return $this->hasMany(BettingHistory::class,'player_id');
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class,'user_to');
    }
    public function transactions_withdraw()
    {
        return $this->hasMany(Transaction::class,'user_from')->orderBy('created_at');
    }
    public function totalHeadBet($current_game_id)
    {
        return $this->bettingHistory()->where('game_rounds_id',$current_game_id)->where('side','heads');
    }
    public function totalTailsBet($current_game_id)
    {
        return $this->bettingHistory()->where('game_rounds_id',$current_game_id)->where('side','tails');
    }

    public function agentId()
    {
        return User::where('code',$this->referral_id);
        // return User::where('code','agent1235');
    }
    public function user_under()
    {
        return User::where('referral_id',$this->code);
        // return User::where('code','agent1235');
    }
    public function user_approval()
    {
        return $this->hasMany(Registration::class,'referral_id','code');
        // return User::where('code','agent1235');
    }
    public function agent_details()
    {
        return $this->hasOne(AgentDetails::class,'user_id');
        // return User::where('code','agent1235');
    }
}
