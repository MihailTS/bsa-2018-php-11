<?php

namespace App\Validators\Wallet;

use App\Exceptions\MarketException\CurrencyDoesNotExistException;
use App\Exceptions\MarketException\IncorrectWalletAmountException;
use App\Exceptions\MarketException\WalletDoesNotExistException;
use App\Repository\Contracts\CurrencyRepository;
use App\Repository\Contracts\MoneyRepository;
use App\Repository\Contracts\WalletRepository;
use App\Request\Contracts\MoneyRequest;
use App\Validators\EntityExistsTrait;

class TakeMoneyValidator
{
    use EntityExistsTrait;

    private $currencyRepository;
    private $walletRepository;
    private $moneyRepository;

    public function __construct(CurrencyRepository $currencyRepository,WalletRepository $walletRepository,
                            MoneyRepository $moneyRepository)
    {
        $this->walletRepository = $walletRepository;
        $this->currencyRepository = $currencyRepository;
        $this->moneyRepository = $moneyRepository;
    }

    /**
     * @param MoneyRequest $request
     * @return bool
     * @throws CurrencyDoesNotExistException
     * @throws WalletDoesNotExistException
     * @throws IncorrectWalletAmountException
     */
    public function validate(MoneyRequest $request)
    {
        $currencyId = $request->getCurrencyId();
        $walletId = $request->getWalletId();
        $amount = $request->getAmount();

        $currency = $this->getCurrencyOrFail($this->currencyRepository, $currencyId);
        $this->getWalletOrFail($this->walletRepository, $walletId);

        $money = $this->moneyRepository->findByWalletAndCurrency($walletId,$currencyId);
        if($money === null || $money->amount < $amount){
            throw new IncorrectWalletAmountException("In wallet with id:$walletId not enough \"$currency->name\" currency for this operation");
        }
        return true;
    }

}