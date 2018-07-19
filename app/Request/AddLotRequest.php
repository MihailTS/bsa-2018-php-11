<?php

namespace App\Request;

use App\Request\Contracts\AddLotRequest as AddLotRequestContract;
use Illuminate\Foundation\Http\FormRequest;

class AddLotRequest extends FormRequest implements AddLotRequestContract
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $dateTimeOpen = $this->getDateTimeOpen();
        $currencyId = $this->getCurrencyId();

        return [
            'currency_id'=>'required|exists:currencies',
            'seller_id'=>"required|exists:users|user_has_not_active_lots:$currencyId",
            'date_time_open'=>'required|integer|min:0',
            'date_time_close'=>"required|integer|min:$dateTimeOpen",
            'price'=>'required|numeric|min:0|max:999999.99',
        ];
    }

    public function getCurrencyId() : int
    {
        return (int) $this->get('currency_id');
    }

    /**
     * An identifier of user
     *
     * @return int
     */
    public function getSellerId() : int
    {
        return (int) $this->get('seller_id');
    }

    /**
     * Timestamp
     *
     * @return int
     */
    public function getDateTimeOpen() : int
    {
        return (int) $this->get('date_time_open');
    }

    /**
     * Timestamp
     *
     * @return int
     */
    public function getDateTimeClose() : int
    {
        return (int) $this->get('date_time_open');
    }

    public function getPrice() : float
    {
        return (float) $this->get('price');
    }

}