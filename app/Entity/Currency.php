<?php

namespace App\Entity;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        "id",
        "name"
    ];

    public function lots()
    {
        return $this->hasMany(Lot::class);
    }

    public function money()
    {
        return $this->hasMany(Money::class);
    }

    public function wallets()
    {
        return $this->belongsToMany(Wallet::class, 'money');
    }

    public function sellers()
    {
        return $this->belongsToMany(User::class, 'lots');
    }
}
