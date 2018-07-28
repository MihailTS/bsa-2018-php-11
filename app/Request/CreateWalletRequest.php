<?php

namespace App\Request;

use App\Request\Contracts\CreateWalletRequest as CreateWalletRequestContract;
use Illuminate\Foundation\Http\FormRequest;

class CreateWalletRequest implements CreateWalletRequestContract
{
    private $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function getUserId() : int
    {
        return (int) $this->userId;
    }
}
