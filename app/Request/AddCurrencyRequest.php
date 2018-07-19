<?php

namespace App\Request;

use App\Request\Contracts\AddCurrencyRequest as AddCurrencyRequestContract;
use Illuminate\Foundation\Http\FormRequest;

class AddCurrencyRequest extends FormRequest implements AddCurrencyRequestContract
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'=>'required',
        ];
    }

    public function getName() : string
    {
        return $this->get('name');
    }
}