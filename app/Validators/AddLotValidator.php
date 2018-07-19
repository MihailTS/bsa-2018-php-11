<?php

namespace App\Validators;


use App\Repository\Contracts\UserRepository;
use Illuminate\Contracts\Validation\Validator;

class AddLotValidator
{
    const ERROR_MESSAGE = 'User have active lots with this currency.';

    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
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
        if (! $parameters || ! $parameters[0]) {
            return false;
        }
        $currencyId = $value;
        $user = $this->userRepository->getById($parameters[0]);
        return $user !== null && $user->currenciesSelling->contains($currencyId);
    }
}