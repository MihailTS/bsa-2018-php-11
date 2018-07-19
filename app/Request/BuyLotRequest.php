<?php

namespace App\Request;

use App\Request\Contracts\BuyLotRequest as BuyLotRequestContract;
use Illuminate\Foundation\Http\FormRequest;

class BuyLotRequest extends FormRequest implements BuyLotRequestContract
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $amount = $this->getAmount();
        $lotId = $this->getLotId();
        return [
            'lot_id'=>'required|exists:lots',
            'user_id'=>"required|exists:users|user_can_buy:$lotId,$amount",
            'amount'=>'required|numeric|min:1|max:999999.99',
        ];
    }

    public function getUserId() : int
    {
        return (int) $this->get('user_id');
    }

    public function getLotId() : int
    {
        return (int) $this->get('lot_id');
    }

    public function getAmount() : float
    {
        return (float) $this->get('amount');
    }
}