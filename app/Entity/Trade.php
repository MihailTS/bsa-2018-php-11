<?php

namespace App\Entity;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    protected $fillable = [
        "id",
        "lot_id",
        "user_id",
        "amount"
    ];

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }


    public function buyer()
    {
        return $this->belongsTo(User::class);
    }
}
