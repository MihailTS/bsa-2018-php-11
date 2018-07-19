<?php

namespace App\Repository;

use App\Repository\Contracts\WalletRepository as WalletRepositoryContract;
use App\Entity\Wallet;

class WalletRepository implements WalletRepositoryContract
{
    public function add(Wallet $wallet) : Wallet
    {
        $wallet->push();

        return $wallet;
    }

    public function findByUser(int $userId) : ?Wallet
    {
        return Wallet::where('user_id',$userId)->first();
    }
}