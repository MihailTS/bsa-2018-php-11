<?php

namespace App\Service;

use App\Repository\Contracts\CurrencyRepository;
use App\Service\Contracts\CurrencyService as CurrencyServiceContract;
use App\Entity\Currency;
use App\Request\Contracts\AddCurrencyRequest;

class CurrencyService implements CurrencyServiceContract
{
    private $currencyRepository;

    public function __construct(CurrencyRepository $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    public function addCurrency(AddCurrencyRequest $currencyRequest) : Currency
    {
        $currency = new Currency;
        $currency->name = $currencyRequest->getName();

        return $this->currencyRepository->add($currency);
    }
}
