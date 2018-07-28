<?php

namespace App\Request;

use App\Request\Contracts\AddLotRequest as AddLotRequestContract;
use Illuminate\Foundation\Http\FormRequest;

class AddLotRequest implements AddLotRequestContract
{
    private $currencyId;
    private $sellerId;
    private $dateTimeOpen;
    private $dateTimeClose;
    private $price;

    public function __construct($currencyId, $sellerId, $dateTimeOpen ,$dateTimeClose, $price)
    {
        $this->currencyId = $currencyId;
        $this->sellerId = $sellerId;
        $this->dateTimeOpen = $dateTimeOpen;
        $this->dateTimeClose = $dateTimeClose;
        $this->price = $price;
    }

    public function getCurrencyId() : int
    {
        return (int) $this->currencyId;
    }

    /**
     * An identifier of user
     *
     * @return int
     */
    public function getSellerId() : int
    {
        return (int) $this->sellerId;
    }

    /**
     * Timestamp
     *
     * @return int
     */
    public function getDateTimeOpen() : int
    {
        return (int) $this->dateTimeOpen;
    }

    /**
     * Timestamp
     *
     * @return int
     */
    public function getDateTimeClose() : int
    {
        return (int) $this->dateTimeClose;
    }

    public function getPrice() : float
    {
        return (float) $this->price;
    }

}