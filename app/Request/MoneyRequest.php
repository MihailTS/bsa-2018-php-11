<?php

namespace App\Request;

use App\Request\Contracts\MoneyRequest as MoneyRequestContract;
use Illuminate\Foundation\Http\FormRequest;

class MoneyRequest implements MoneyRequestContract
{
    private $walletId;
    private $currencyId;
    private $amount;

    public function __construct($walletId, $currencyId, $amount)
    {
        $this->walletId = $walletId;
        $this->currencyId = $currencyId;
        $this->amount = $amount;
    }

    public function getWalletId() : int
    {
        return (int) $this->walletId;
    }

    public function getCurrencyId() : int
    {
        return (int) $this->currencyId;
    }

    public function getAmount() : float
    {
        return (float) $this->amount;
    }
}