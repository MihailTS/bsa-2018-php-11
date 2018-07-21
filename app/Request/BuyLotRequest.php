<?php

namespace App\Request;

use App\Request\Contracts\BuyLotRequest as BuyLotRequestContract;
use Illuminate\Foundation\Http\FormRequest;

class BuyLotRequest implements BuyLotRequestContract
{
    private $userId;
    private $lotId;
    private $amount;

    public function __construct($userId, $lotId, $amount)
    {
        $this->userId = $userId;
        $this->lotId = $lotId;
        $this->amount = $amount;
    }

    public function getUserId() : int
    {
        return (int) $this->userId;
    }

    public function getLotId() : int
    {
        return (int) $this->lotId;
    }

    public function getAmount() : float
    {
        return (float) $this->amount;
    }
}
