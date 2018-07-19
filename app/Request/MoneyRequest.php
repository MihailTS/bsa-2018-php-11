<?php

namespace App\Request;

use App\Request\Contracts\MoneyRequest as MoneyRequestContract;
use Illuminate\Foundation\Http\FormRequest;

class MoneyRequest extends FormRequest implements MoneyRequestContract
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'wallet_id'=>'required|exists:wallets',
            'currency_id'=>'required|exists:currencies',
            'amount'=>'required|amount|min:0|max:999999.99',
        ];
    }
    public function getWalletId() : int
    {
        return (int) $this->get('wallet_id');
    }

    public function getCurrencyId() : int
    {
        return (int) $this->get('currency_id');
    }

    public function getAmount() : float
    {
        return (float) $this->get('amount');
    }
}