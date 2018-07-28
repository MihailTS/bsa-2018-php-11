<?php

namespace App\Service;

use App\Repository\Contracts\MoneyRepository;
use App\Repository\Contracts\WalletRepository;
use App\Service\Contracts\WalletService as WalletServiceContract;
use App\Entity\Money;
use App\Entity\Wallet;
use App\Request\Contracts\CreateWalletRequest;
use App\Request\Contracts\MoneyRequest;

class WalletService implements WalletServiceContract
{
    private $moneyRepository;
    private $walletRepository;

    public function __construct(WalletRepository $walletRepository,MoneyRepository $moneyRepository)
    {
        $this->walletRepository = $walletRepository;
        $this->moneyRepository = $moneyRepository;
    }

    /**
     * Add wallet to user.
     *
     * @param CreateWalletRequest $walletRequest
     * @return Wallet
     */
    public function addWallet(CreateWalletRequest $walletRequest) : Wallet
    {
        $wallet = new Wallet;
        $wallet->user_id = $walletRequest->getUserId();
        return $this->walletRepository->add($wallet);
    }

    /**
     * Add money to a wallet.
     *
     * @return Money
     */
    public function addMoney(MoneyRequest $moneyRequest) : Money
    {
        $walletId = $moneyRequest->getWalletId();
        $amount = $moneyRequest->getAmount();
        $currencyId = $moneyRequest->getCurrencyId();

        $money = $this->moneyRepository->findByWalletAndCurrency($walletId,$currencyId);
        if($money === null){
            $money = new Money;
            $money->wallet_id = $walletId;
            $money->amount = $amount;
            $money->currency_id = $currencyId;
        }else{
            $money->amount += $amount;
        }

        $money->wallet_id = $moneyRequest->getWalletId();
        $money->amount = $moneyRequest->getAmount();
        $money->currency_id = $moneyRequest->getCurrencyId();

        return $this->moneyRepository->save($money);
    }

    /**
     * Take money from a wallet.
     *
     * @param MoneyRequest $moneyRequest
     * @return Money
     */
    public function takeMoney(MoneyRequest $moneyRequest) : Money
    {
        $walletId = $moneyRequest->getWalletId();
        $amount = $moneyRequest->getAmount();
        $currencyId = $moneyRequest->getCurrencyId();

        $money = $this->moneyRepository->findByWalletAndCurrency($walletId,$currencyId);

        $money->amount -= $amount;

        return $this->moneyRepository->save($money);
    }
}