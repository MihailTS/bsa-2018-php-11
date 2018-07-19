<?php

namespace App;

use App\Entity\Currency;
use App\Entity\Lot;
use App\Entity\Trade;
use App\Entity\Wallet;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function lots()
    {
        return $this->hasMany(Lot::class);
    }

    public function trades()
    {
        return $this->hasMany(Trade::class);
    }

    public function boughtLots()
    {
        return $this->belongsToMany(Lot::class, 'trades');
    }

    public function currenciesSelling()
    {
        return $this->belongsToMany(Currency::class, 'lots');
    }
}
