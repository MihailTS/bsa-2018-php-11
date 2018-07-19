<?php

namespace App\Repository;

use App\Repository\Contracts\TradeRepository as TradeRepositoryContract;
use App\Entity\Trade;

class TradeRepository implements TradeRepositoryContract
{
    public function add(Trade $trade) : Trade
    {
        $trade->push();

        return $trade;
    }
}