<?php

namespace App\Http\Requests\Lots;

use Illuminate\Foundation\Http\FormRequest;

class StoreLotRequest extends FormRequest
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
        $dateTimeOpen = $this->get('date_time_open');
        return [
            'currency_id' => 'required|integer|min:0',
            'seller_id' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0|max:999999.99',
            'date_time_open' => 'required|integer|min:0|max:2147483647',
            'date_time_close' => "required|integer|min:$dateTimeOpen|max:2147483647",
        ];
    }

    public function getCurrencyId(): int
    {
        return (int)$this->get('currency_id');
    }

    public function getSellerId(): int
    {
        return (int)$this->get('seller_id');
    }

    public function getDateTimeOpen(): int
    {
        return (int)$this->get('date_time_open');
    }

    public function getDateTimeClose(): int
    {
        return (int)$this->get('date_time_close');
    }


    public function getPrice(): float
    {
        return (float)$this->get('price');
    }
}
