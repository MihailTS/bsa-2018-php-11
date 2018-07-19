<?php

namespace App\Validators;


use App\Repository\Contracts\LotRepository;
use App\Repository\Contracts\UserRepository;
use App\Repository\MoneyRepository;
use Illuminate\Contracts\Validation\Validator;

class BuyLotValidator
{
    const ERROR_MESSAGE = 'User has not enough money.';

    private $userRepository;
    private $moneyRepository;
    private $lotRepository;

    public function __construct(UserRepository $userRepository, MoneyRepository $moneyRepository,
                                LotRepository $lotRepository)
    {
        $this->userRepository = $userRepository;
        $this->moneyRepository = $moneyRepository;
        $this->lotRepository = $lotRepository;
    }
    /**
     *
     * @param  $attribute
     * @param  $value
     * @param  $parameters
     * @param \Illuminate\Contracts\Validation\Validator $validator
     *
     * @return bool
     */
    public function validate(
        $attribute,
        $value,
        $parameters,
        Validator $validator
    ): bool {
        if (! $parameters || ! $parameters[0]|| ! $parameters[1]) {
            return false;
        }
        $userId = $value;
        $lotId = $parameters[0];
        $amount = $parameters[1];

        $lot = $this->lotRepository->getById($lotId);
        $sellerId = $lot->seller->wallet->id;
        $money = $this->moneyRepository->findByWalletAndCurrency($sellerId,$lot->currency_id);
        return $userId!=$sellerId && $money->amount >= $amount;
    }
}