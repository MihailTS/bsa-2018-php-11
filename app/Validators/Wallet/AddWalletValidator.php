<?php

namespace App\Validators\Wallet;


use App\Exceptions\MarketException\UserHasWalletException;
use App\Repository\Contracts\UserRepository;
use App\Repository\Contracts\WalletRepository;
use App\Request\Contracts\CreateWalletRequest;
use App\Validators\EntityExistsTrait;

class AddWalletValidator
{
    use EntityExistsTrait;

    private $walletRepository;
    private $userRepository;

    public function __construct(WalletRepository $walletRepository,
            UserRepository $userRepository)
    {
        $this->walletRepository = $walletRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param CreateWalletRequest $request
     * @return bool
     * @throws UserHasWalletException
     * @throws \App\Exceptions\MarketException\UserDoesNotExistException
     */
    public function validate(CreateWalletRequest $request)
    {
        $userId = $request->getUserId();

        $this->getUserOrFail($this->userRepository, $userId);
        $this->validateUserHasNotWallet($userId);

        return true;
    }

    /**
     * @param int $userId
     * @return bool
     * @throws UserHasWalletException
     */
    protected function validateUserHasNotWallet(int $userId)
    {
        $wallet = $this->walletRepository->findByUser($userId);
        if($wallet !== null){
            throw new UserHasWalletException('User wallet already exists');
        }
        return true;
    }

}