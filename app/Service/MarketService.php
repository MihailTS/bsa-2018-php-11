<?php

namespace App\Service;

use App\Mail\TradeCreated;
use App\Repository\Contracts\CurrencyRepository;
use App\Repository\Contracts\LotRepository;
use App\Repository\Contracts\TradeRepository;
use App\Repository\Contracts\UserRepository;
use App\Repository\Contracts\MoneyRepository;
use App\Repository\Contracts\WalletRepository;
use App\Request\MoneyRequest;
use App\Service\Contracts\WalletService;
use App\Service\Contracts\MarketService as MarketServiceContract;
use App\Entity\{
    Lot, Money, Trade
};
use App\Request\Contracts\{ AddLotRequest, BuyLotRequest };
use App\Response\Contracts\LotResponse as LotResponseContract;
use App\Response\LotResponse;
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
    private $userRepository;
    private $addLotValidator;
    private $buyLotValidator;
    private $walletRepository;
    private $currencyRepository;
    private $walletService;
    private $moneyRepository;

    public function __construct(LotRepository $lotRepository, TradeRepository $tradeRepository,
                                WalletRepository $walletRepository, UserRepository $userRepository,
                                CurrencyRepository $currencyRepository,MoneyRepository $moneyRepository,
                                WalletService $walletService,
                                AddLotValidator $addLotValidator,BuyLotValidator $buyLotValidator)
    {
        $this->lotRepository = $lotRepository;
        $this->tradeRepository = $tradeRepository;
        $this->userRepository = $userRepository;
        $this->walletRepository = $walletRepository;
        $this->currencyRepository = $currencyRepository;
        $this->moneyRepository = $moneyRepository;

        $this->walletService = $walletService;

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


        $trade = new Trade;
        $trade->lot_id = $lotId;
        $trade->user_id = $userId;
        $trade->amount = $amount;

        $lot = $this->lotRepository->getById($lotId);

        $sellerWallet = $this->walletRepository->findByUser($lot->seller_id);
        $this->walletService->takeMoney(new MoneyRequest($sellerWallet->id,$lot->currency_id,$amount));


        $buyerWallet = $this->walletRepository->findByUser($userId);
        $this->walletService->addMoney(new MoneyRequest($buyerWallet->id,$lot->currency_id,$amount));


        $buyer = $this->userRepository->getById($userId);
        $seller = $this->userRepository->getById($lot->seller_id);
        $currency = $this->currencyRepository->getById($lot->currency_id);
        $rateChangedMessage = new TradeCreated($trade, $seller, $buyer, $currency);

        Mail::to($lot->seller)->send($rateChangedMessage->build());

        return $this->tradeRepository->add($trade);
    }

    /**
     * Retrieves lot by an identifier and returns it in LotResponse format
     *
     * @param int $id
     *
     * @return LotResponseContract
     */
    public function getLot(int $id) : LotResponseContract
    {
        $lot = $this->lotRepository->getById($id);
        return new LotResponse(
            $this->moneyRepository,
            $this->walletRepository,
            $this->currencyRepository,
            $this->userRepository,
            $lot
        );
    }

    /**
     * Return list of lots.
     *
     * @return LotResponse[]
     */
    public function getLotList() : array
    {
        $lots = $this->lotRepository->findAll();
        return array_map(
                function($lot){
                    return new LotResponse(
                        $this->moneyRepository,
                        $this->walletRepository,
                        $this->currencyRepository,
                        $this->userRepository,
                        $lot
                    );
                },
                $lots
        );
    }
}
