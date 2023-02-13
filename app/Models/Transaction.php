<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Transaction extends Model
{
    use HasFactory;
    // protected $table = 'transactions';
    public function from()
    {
        return $this->hasOne(User::class,'id','user_from');
    }
    public function to()
    {
        return $this->hasOne(User::class,'id','user_to');
    }

}
