<?php

namespace App\Validators\Market;

use App\Exceptions\MarketException\BuyInactiveLotException;
use App\Exceptions\MarketException\BuyNegativeAmountException;
use App\Exceptions\MarketException\BuyOwnCurrencyException;
use App\Exceptions\MarketException\IncorrectLotAmountException;
use App\Exceptions\MarketException\UserDoesNotExistException;
use App\Exceptions\MarketException\WalletDoesNotExistException;
use App\Repository\Contracts\LotRepository;
use App\Repository\Contracts\MoneyRepository;
use App\Repository\Contracts\UserRepository;
use App\Repository\Contracts\WalletRepository;
use App\Request\Contracts\BuyLotRequest;
use App\Validators\EntityExistsTrait;

class BuyLotValidator
{
    use EntityExistsTrait;

    private $userRepository;
    private $lotRepository;
    private $walletRepository;
    private $moneyRepository;

    public function __construct(UserRepository $userRepository, MoneyRepository $moneyRepository,
                                WalletRepository $walletRepository, LotRepository $lotRepository)
    {
        $this->userRepository = $userRepository;
        $this->lotRepository = $lotRepository;
        $this->walletRepository = $walletRepository;
        $this->moneyRepository = $moneyRepository;
    }

    /**
     * @param BuyLotRequest $request
     * @return bool
     * @throws UserDoesNotExistException
     * @throws BuyNegativeAmountException
     * @throws IncorrectLotAmountException
     * @throws WalletDoesNotExistException
     * @throws BuyOwnCurrencyException
     * @throws BuyInactiveLotException
     */
    public function validate(BuyLotRequest $request)
    {
        $userId = $request->getUserId();
        $lotId = $request->getLotId();
        $amount = $request->getAmount();

        if ($amount < 0) {
            throw new BuyNegativeAmountException("Amount must be positive");
        }

        if ($amount < 1) {
            throw new IncorrectLotAmountException("Currency amount can't be less than 1");
        }

        //$this->getUserOrFail($this->userRepository, $userId);
        $lot = $this->getLotOrFail($this->lotRepository, $lotId);


        $sellerId = $lot->seller_id;
        if($sellerId === $userId){
            throw new BuyOwnCurrencyException("User can't buy own currency");
        }

        $currentTimeStamp = now()->timestamp;
        if ($currentTimeStamp > $lot->date_time_close || $currentTimeStamp < $lot->date_time_open) {
            throw new BuyInactiveLotException("Lot $lotId isn't active");
        }

        $wallet = $this->walletRepository->findByUser($sellerId);
        if($wallet === null)
        {
            throw new WalletDoesNotExistException("User with id:$userId hasn't wallet");
        }
        $money = $this->moneyRepository->findByWalletAndCurrency($wallet->id,$lot->currency_id);
        if($money === null || $money->amount < $amount)
        {
            throw new IncorrectLotAmountException("Not enough money in lot for this operation");
        }

        return true;
    }

}