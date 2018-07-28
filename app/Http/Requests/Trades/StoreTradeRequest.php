<?php

namespace App\Http\Requests\Trades;

use Illuminate\Foundation\Http\FormRequest;

class StoreTradeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'lot_id' => 'required|integer|min:0',
            'amount' => 'required|numeric|max:999999.99',
        ];
    }

    public function getLotId(): int
    {
        return (int)$this->get('lot_id');
    }

    public function getAmount(): float
    {
        return (float)$this->get('amount');
    }

}
