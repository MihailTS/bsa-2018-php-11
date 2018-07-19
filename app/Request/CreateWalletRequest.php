<?php

namespace App\Request;

use App\Request\Contracts\CreateWalletRequest as CreateWalletRequestContract;
use Illuminate\Foundation\Http\FormRequest;

class CreateWalletRequest extends FormRequest implements CreateWalletRequestContract
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id'=>'required|exists:users|has_not_wallet',
        ];
    }
    public function getUserId() : int
    {
        return (int) $this->get('user_id');
    }
}
