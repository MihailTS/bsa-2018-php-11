<?php

namespace App\Request;

use App\Request\Contracts\AddCurrencyRequest as AddCurrencyRequestContract;
use Illuminate\Foundation\Http\FormRequest;

class AddCurrencyRequest implements AddCurrencyRequestContract
{
    private $name;

    public function __construct($name)
    {
        $this->name=$name;
    }

    public function getName() : string
    {
        return $this->name;
    }
}