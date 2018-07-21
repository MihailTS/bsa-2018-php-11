<?php

namespace App\Repository;

use App\Entity\Lot;
use App\Repository\Contracts\LotRepository as LotRepositoryContract;

class LotRepository implements LotRepositoryContract
{
    /**
     * @param Lot $lot
     * @return Lot
     */
    public function add(Lot $lot) : Lot
    {
        $lot->push();

        return $lot;
    }

    /**
     * @param int $id
     * @return Lot
     */
    public function getById(int $id) : Lot
    {
        return Lot::find($id);
    }

    public function findAll(){
        return Lot::all();
    }

    public function findActiveLot(int $userId): ?Lot
    {
        return Lot::where('seller_id',$userId)->active()->first();
    }

    public function findActiveLots(int $userId): array
    {
        return Lot::where('seller_id',$userId)->active()->get();
    }
}
