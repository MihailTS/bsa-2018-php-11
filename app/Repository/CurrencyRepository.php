<?php

namespace App\Repository;

use App\Entity\Currency;
use App\Repository\Contracts\CurrencyRepository as CurrencyRepositoryContract;

class CurrencyRepository implements CurrencyRepositoryContract
{

    /**
     * @param Currency $currency
     * @return Currency
     */
    public function add(Currency $currency) : Currency
    {
        $currency->save();

        return $currency;
    }

    /**
     * @param int $id
     * @return Currency
     */
    public function getById(int $id) : Currency
    {
        return Currency::find($id);
    }

    /**
     * @param string $name
     * @return Currency|null
     */
    public function getCurrencyByName(string $name): ?Currency
    {
        return Currency::where('name',$name)->first();
    }

    /**
     * @return Currency[]
     */
    public function findAll()
    {
        return Currency::all();
    }
}
