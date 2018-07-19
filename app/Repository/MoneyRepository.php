<?php

namespace App\Repository;

use App\Entity\Money;
use App\Repository\Contracts\MoneyRepository as MoneyRepositoryContract;

class MoneyRepository implements MoneyRepositoryContract
{
    /**
     * @param Money $money
     * @return Money
     */
    public function save(Money $money) : Money
    {
        $money->push();

        return $money;
    }

    /**
     * @param int $walletId
     * @param int $currencyId
     * @return Money|null
     */
    public function findByWalletAndCurrency(int $walletId, int $currencyId) : ?Money
    {
        return Money::where([
            'wallet_id' => $walletId,
            'currency_id' => $currencyId
        ])->first();
    }
}
