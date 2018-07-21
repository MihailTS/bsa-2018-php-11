<?php

namespace App\Validators;


use App\Entity\Currency;
use App\Entity\Lot;
use App\Entity\Wallet;
use App\Exceptions\MarketException\CurrencyDoesNotExistException;
use App\Exceptions\MarketException\LotDoesNotExistException;
use App\Exceptions\MarketException\UserDoesNotExistException;
use App\Exceptions\MarketException\WalletDoesNotExistException;
use App\Repository\Contracts\LotRepository;
use App\Repository\Contracts\WalletRepository;
use App\Repository\Contracts\CurrencyRepository;
use App\Repository\Contracts\UserRepository;
use App\User;

trait EntityExistsTrait
{
    /**
     * @param CurrencyRepository $currencyRepository
     * @param int $currencyId
     * @return Currency
     * @throws CurrencyDoesNotExistException
     */
    protected function getCurrencyOrFail(CurrencyRepository $currencyRepository, $currencyId)
    {
        $currency = $currencyRepository->getById($currencyId);
        if($currency === null){
            throw new CurrencyDoesNotExistException("Currency with id:$currencyId doesn't exist");
        }
        return $currency;
    }

    /**
     * @param WalletRepository $walletRepository
     * @param int $walletId
     * @return Wallet
     * @throws WalletDoesNotExistException
     */
    protected function getWalletOrFail(WalletRepository $walletRepository,int $walletId)
    {
        $wallet = $walletRepository->getById($walletId);
        if($wallet === null){
            throw new WalletDoesNotExistException("Wallet with id:$walletId doesn't exist");
        }
        return $wallet;
    }

    /**
     * @param UserRepository $userRepository
     * @param int $userId
     * @return User
     * @throws UserDoesNotExistException
     */
    protected function getUserOrFail(UserRepository $userRepository,int $userId)
    {
        $user = $userRepository->getById($userId);
        if($user === null){
            throw new UserDoesNotExistException("User with id:$userId doesn't exist");
        }
        return $user;
    }


    /**
     * @param LotRepository $lotRepository
     * @param int $lotId
     * @return Lot
     * @throws LotDoesNotExistException
     */
    protected function getLotOrFail(LotRepository $lotRepository,int $lotId)
    {
        $lot = $lotRepository->getById($lotId);
        if($lot === null){
            throw new LotDoesNotExistException("Lot with id:$lotId doesn't exist");
        }
        return $lot;
    }
}