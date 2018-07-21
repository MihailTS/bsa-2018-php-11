<?php

namespace App\Validators\Market;

use App\Exceptions\MarketException\IncorrectLotAmountException;
use App\Exceptions\MarketException\IncorrectPriceException;
use App\Exceptions\MarketException\IncorrectTimeCloseException;
use App\Exceptions\MarketException\UserDoesNotExistException;
use App\Exceptions\MarketException\CurrencyDoesNotExistException;
use App\Repository\Contracts\CurrencyRepository;
use App\Repository\Contracts\LotRepository;
use App\Repository\Contracts\UserRepository;
use App\Request\Contracts\AddLotRequest;
use App\Validators\EntityExistsTrait;

class AddLotValidator
{
    use EntityExistsTrait;

    private $currencyRepository;
    private $userRepository;
    private $lotRepository;

    public function __construct(CurrencyRepository $currencyRepository,UserRepository $userRepository,
                                LotRepository $lotRepository)
    {
        $this->currencyRepository = $currencyRepository;
        $this->userRepository = $userRepository;
        $this->lotRepository = $lotRepository;
    }

    /**
     * @param AddLotRequest $request
     * @return bool
     * @throws IncorrectPriceException
     * @throws IncorrectTimeCloseException
     * @throws UserDoesNotExistException
     * @throws CurrencyDoesNotExistException
     * @throws IncorrectLotAmountException
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

        //$this->getCurrencyOrFail($this->currencyRepository, $currencyId);
        //$this->getUserOrFail($this->userRepository, $sellerId);

        $activeLots = $this->lotRepository->findActiveLots($sellerId);
        foreach($activeLots as $activeLot){
            if($activeLot->currency->id === $currencyId)
            {
                throw new IncorrectLotAmountException("User already has active currency lot");
            }
        }
        return true;
    }

}