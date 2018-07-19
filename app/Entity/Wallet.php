<?php

namespace App\Entity;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        "id",
        "user_id"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function money()
    {
        return $this->hasMany(Money::class);
    }

    public function currencies()
    {
        return $this->belongsToMany(Currency::class,'money');
    }
}
