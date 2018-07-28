<?php

namespace App\Entity;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Lot extends Model
{
    protected $fillable = [
        "id",
        'currency_id',
        'seller_id',
        'date_time_open',
        'date_time_close',
        'price'
    ];

    protected $dates = [
        'date_time_open',
        'date_time_close'
    ];
    public function scopeActive($query)
    {
        return $query->where('date_time_open','<',now())->where('date_time_close','>',now());
    }

    public function getDateTimeOpen(): int
    {
        if (is_int($this->date_time_open)) {
            return $this->date_time_open;
        } else {
            return (new Carbon($this->date_time_open))->getTimestamp();
        }
    }

    public function getDateTimeClose(): int
    {
        if (is_int($this->date_time_close)) {
            return $this->date_time_close;
        } else {
            return (new Carbon($this->date_time_close))->getTimestamp();
        }
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }


    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function trades()
    {
        return $this->hasMany(Trade::class);
    }

    public function buyers()
    {
        return $this->belongsToMany(User::class,'trades');
    }
}
