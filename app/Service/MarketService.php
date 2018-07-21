<?php

namespace App\Service;

use App\Mail\TradeCreated;
use App\Repository\Contracts\LotRepository;
use App\Repository\Contracts\TradeRepository;
use App\Repository\Contracts\UserRepository;
use App\Repository\Contracts\MoneyRepository;
use App\Service\Contracts\MarketService as MarketServiceContract;
use App\Entity\{
    Lot, Money, Trade
};
use App\Request\Contracts\{ AddLotRequest, BuyLotRequest };
use App\Response\Contracts\LotResponse;
use App\Exceptions\MarketException\{
    ActiveLotExistsException,
    IncorrectPriceException,
    IncorrectTimeCloseException,
    BuyOwnCurrencyException,
    IncorrectLotAmountException,
    BuyNegativeAmountException,
    BuyInactiveLotException,
    LotDoesNotExistException
};
use App\Validators\Market\AddLotValidator;
use App\Validators\Market\BuyLotValidator;

use Mail;

class MarketService implements MarketServiceContract
{
    private $lotRepository;
    private $tradeRepository;
    private $moneyRepository;
    private $userRepository;
    private $addLotValidator;
    private $buyLotValidator;

    public function __construct(LotRepository $lotRepository, TradeRepository $tradeRepository,
                                MoneyRepository $moneyRepository, UserRepository $userRepository,
                                AddLotValidator $addLotValidator,BuyLotValidator $buyLotValidator)
    {
        $this->lotRepository = $lotRepository;
        $this->tradeRepository = $tradeRepository;
        $this->moneyRepository = $moneyRepository;
        $this->userRepository = $userRepository;

        $this->addLotValidator = $addLotValidator;
        $this->buyLotValidator = $buyLotValidator;
    }

    /**
     * Sell currency.
     *
     * @param AddLotRequest $lotRequest
     * 
     * @throws ActiveLotExistsException
     * @throws IncorrectTimeCloseException
     * @throws IncorrectPriceException
     *
     * @return Lot
     */
    public function addLot(AddLotRequest $lotRequest) : Lot
    {
        $this->addLotValidator->validate($lotRequest);
        $lot = new Lot;
        $lot->currency_id = $lotRequest->getCurrencyId();
        $lot->seller_id = $lotRequest->getSellerId();
        $lot->price = $lotRequest->getPrice();
        $lot->date_time_open = $lotRequest->getDateTimeOpen();
        $lot->date_time_close = $lotRequest->getDateTimeClose();

        return $this->lotRepository->add($lot);
    }

    /**
     * Buy currency.
     *
     * @param BuyLotRequest $lotRequest
     * 
     * @throws BuyOwnCurrencyException
     * @throws IncorrectLotAmountException
     * @throws BuyNegativeAmountException
     * @throws BuyInactiveLotException
     * 
     * @return Trade
     */
    public function buyLot(BuyLotRequest $lotRequest) : Trade
    {
        $this->buyLotValidator->validate($lotRequest);
        $lotId = $lotRequest->getLotId();
        $userId = $lotRequest->getUserId();
        $amount = $lotRequest->getAmount();

        $buyer = $this->userRepository->getById($userId);

        $trade = new Trade;
        $trade->lot_id = $lotId;
        $trade->user_id = $userId;
        $trade->amount = $amount;

        $lot = $this->lotRepository->getById($lotId);

        $sellerMoney = $this->moneyRepository->findByWalletAndCurrency($lot->seller->wallet->id,$lot->currency_id);
        $sellerMoney->amount-=$amount;
        $this->moneyRepository->save($sellerMoney);

        $buyerMoney = $this->moneyRepository->findByWalletAndCurrency($buyer->wallet->id,$lot->currency_id);
        $buyerMoney->amount-=$amount;
        $this->moneyRepository->save($buyerMoney);

        $rateChangedMessage = new TradeCreated($trade);
        Mail::to($lot->seller)->send($rateChangedMessage->build());

        return $this->tradeRepository->add($trade);
    }

    /**
     * Retrieves lot by an identifier and returns it in LotResponse format
     *
     * @param int $id
     *
     * @return LotResponse
     */
    public function getLot(int $id) : LotResponse
    {
        $lot = $this->lotRepository->getById($id);
        return new App\Response\LotResponse($lot);
    }

    /**
     * Return list of lots.
     *
     * @return LotResponse[]
     */
    public function getLotList() : array
    {
        $lots = $this->lotRepository->findAll();
        return array_map(function($lot){
            return new App\Response\LotResponse($lot);
            },$lots);
    }
}
