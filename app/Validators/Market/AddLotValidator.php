<?php

namespace App\Validators\Market;

use App\Exceptions\MarketException\ActiveLotExistsException;
use App\Exceptions\MarketException\IncorrectPriceException;
use App\Exceptions\MarketException\IncorrectTimeCloseException;
use App\Repository\Contracts\LotRepository;
use App\Request\Contracts\AddLotRequest;

class AddLotValidator
{

    private $lotRepository;

    public function __construct(LotRepository $lotRepository)
    {
        $this->lotRepository = $lotRepository;
    }

    /**
     * @param AddLotRequest $request
     * @return bool
     * @throws IncorrectPriceException
     * @throws IncorrectTimeCloseException
     * @throws ActiveLotExistsException
     */
    public function validate(AddLotRequest $request)
    {
        $currencyId = $request->getCurrencyId();
        $sellerId = $request->getSellerId();
        $dateTimeOpen = $request->getDateTimeOpen();
        $dateTimeClose = $request->getDateTimeClose();
        $price = $request->getPrice();

        if ($price < 0) {
            throw new IncorrectPriceException("Price must be positive");
        }

        if ($dateTimeClose < $dateTimeOpen) {
            throw new IncorrectTimeCloseException("Close datetime can't be before open");
        }

        $activeLots = $this->lotRepository->findActiveLots($sellerId);
        foreach($activeLots as $activeLot){
            if($activeLot->currency_id === $currencyId)
            {
                throw new ActiveLotExistsException("User already has active currency lot");
            }
        }
        return true;
    }

}