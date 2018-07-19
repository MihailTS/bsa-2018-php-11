<?php

namespace App\Validators;


use App\Repository\Contracts\UserRepository;
use Illuminate\Contracts\Validation\Validator;

class CreateWalletValidator
{
    const ERROR_MESSAGE = 'User already have wallet';

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
        $userId = $value;
        $user = $this->userRepository->getById($userId);
        return $user->wallet();
    }
}