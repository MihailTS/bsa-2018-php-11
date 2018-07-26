<?php

namespace App\Response;

use App\Repository\Contracts\MoneyRepository;
use App\Repository\Contracts\UserRepository;
use App\Repository\Contracts\WalletRepository;
use App\Repository\Contracts\CurrencyRepository;
use App\Response\Contracts\LotResponse as LotResponseContract;
use App\Entity\Lot;
use Carbon\Carbon;
use Response;

class LotResponse extends Response implements LotResponseContract
{
    private $moneyRepository;
    private $walletRepository;
    private $currencyRepository;
    private $userRepository;
    private $lot;
    
    public function __construct(MoneyRepository $moneyRepository, WalletRepository $walletRepository,
                                CurrencyRepository $currencyRepository, UserRepository $userRepository,
                                Lot $lot)
    {
        $this->moneyRepository = $moneyRepository;
        $this->walletRepository = $walletRepository;
        $this->currencyRepository = $currencyRepository;
        $this->userRepository = $userRepository;
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
        return $this->userRepository->getById($this->lot->seller_id)->name;
    }

    public function getCurrencyName(): string
    {
        return $this->currencyRepository->getById($this->lot->currency_id)->name;
    }

    /*
    * All amount of currency that user has in the wallet.
    *
    * @return float
    */
    public function getAmount(): float
    {
        $wallet = $this->walletRepository->findByUser($this->lot->seller_id);
        $money = $this->moneyRepository->findByWalletAndCurrency($wallet->id,$this->lot->currency_id);
        return $money->amount;
    }

    /*
    * Format: yyyy/mm/dd hh:mm:ss
    *
    * @return string
    */
    public function getDateTimeOpen(): string
    {
        return Carbon::createFromTimestamp($this->lot->date_time_open)->format('Y/m/d h:i:s');
    }

    /*
    * Format: yyyy/mm/dd hh:mm:ss
    *
    * @return string
    */
    public function getDateTimeClose(): string
    {
        return Carbon::createFromTimestamp($this->lot->date_time_close)->format('Y/m/d h:i:s');
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