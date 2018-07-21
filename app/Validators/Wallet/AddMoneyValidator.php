<?php

namespace App\Validators\Wallet;

use App\Exceptions\MarketException\BuyNegativeAmountException;
use App\Repository\Contracts\CurrencyRepository;
use App\Repository\Contracts\WalletRepository;
use App\Request\Contracts\MoneyRequest;
use App\Validators\EntityExistsTrait;

class AddMoneyValidator
{
    use EntityExistsTrait;

    private $currencyRepository;
    private $walletRepository;

    public function __construct(CurrencyRepository $currencyRepository,
                                WalletRepository $walletRepository)
    {
        $this->walletRepository = $walletRepository;
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * @param MoneyRequest $request
     * @return bool
     * @throws BuyNegativeAmountException
     * @throws \App\Exceptions\MarketException\CurrencyDoesNotExistException
     * @throws \App\Exceptions\MarketException\WalletDoesNotExistException
     */
    public function validate(MoneyRequest $request)
    {
        $this->getCurrencyOrFail($this->currencyRepository, $request->getCurrencyId());
        $this->getWalletOrFail($this->walletRepository, $request->getWalletId());

        return true;
    }

}