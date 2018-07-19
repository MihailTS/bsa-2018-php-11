<?php

namespace App\Response;

use App\Response\Contracts\LotResponse as LotResponseContract;
use App\Entity\Lot;
use Carbon\Carbon;
use Response;

class LotResponse extends Response implements LotResponseContract
{
    private $lot;

    public function __construct(Lot $lot)
    {
        $this->lot = $lot;
    }

    /*
    * An identifier of lot
    *
    * @return int
    */
    public function getId(): int
    {
        return $this->lot->id;
    }

    public function getUserName(): string
    {
        return $this->lot->seller->name;
    }

    public function getCurrencyName(): string
    {
        return $this->lot->currency->name;
    }

    /*
    * All amount of currency that user has in the wallet.
    *
    * @return float
    */
    public function getAmount(): float
    {
        return $this->lot->seller->wallet->amount;
    }

    /*
    * Format: yyyy/mm/dd hh:mm:ss
    *
    * @return string
    */
    public function getDateTimeOpen(): string
    {
        return Carbon::createFromTimestamp($this->lot->date_time_open)->format('Y/m/d');
    }

    /*
    * Format: yyyy/mm/dd hh:mm:ss
    *
    * @return string
    */
    public function getDateTimeClose(): string
    {
        return Carbon::createFromTimestamp($this->lot->date_time_close)->format('Y/m/d');
    }

    /**
     * Price per one amount of currency.
     *
     * Format: 00,00
     *
     * @return string
     */
    public function getPrice(): string
    {
        return number_format($this->lot->price, 2, ',', '');
    }
}