<?php

namespace Tests\Unit;

use App\Entity\Currency;
use App\Entity\Lot;
use App\Entity\Trade;
use App\Exceptions\MarketException\IncorrectLotAmountException;
use App\Exceptions\MarketException\IncorrectPriceException;
use App\Validators\Market\AddLotValidator;
use App\Validators\Market\BuyLotValidator;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;


use App\Repository\Contracts\CurrencyRepository;
use App\Repository\Contracts\LotRepository;
use App\Repository\Contracts\MoneyRepository;
use App\Repository\Contracts\TradeRepository;
use App\Repository\Contracts\UserRepository;
use App\Repository\Contracts\WalletRepository;

use App\Service\MarketService;

use App\Response\Contracts\LotResponse;

use App\Request\AddLotRequest;
use App\Request\BuyLotRequest;

use Carbon\Carbon;

class MarketServiceTest extends TestCase
{
    private $lotRepository;
    private $userRepository;
    private $currencyRepository;
    private $tradeRepository;
    private $moneyRepository;
    private $walletRepository;

    private $marketService;

    protected function setUp()
    {
        parent::setUp();

        $this->lotRepository = $this->createMock(LotRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->tradeRepository = $this->createMock(TradeRepository::class);
        $this->currencyRepository = $this->createMock(CurrencyRepository::class);
        $this->walletRepository = $this->createMock(WalletRepository::class);
        $this->moneyRepository = $this->createMock(MoneyRepository::class);

        $this->lotRepository->method('add')->will($this->returnArgument(0));
        $this->tradeRepository->method('add')->will($this->returnArgument(0));
        $this->currencyRepository->method('add')->will($this->returnArgument(0));
        $this->walletRepository->method('add')->will($this->returnArgument(0));
        $this->moneyRepository->method('save')->will($this->returnArgument(0));

        $addLotValidator = new AddLotValidator(
          $this->currencyRepository,
          $this->userRepository,
          $this->lotRepository
        );
        $buyLotValidator = new BuyLotValidator(
            $this->userRepository,
            $this->moneyRepository,
            $this->walletRepository,
            $this->lotRepository
        );
        $this->marketService = new MarketService(
             $this->lotRepository,
             $this->tradeRepository,
             $this->moneyRepository,
             $this->userRepository,
             $addLotValidator,
             $buyLotValidator
        );
    }
    /**
     * @dataProvider addLotDataProvider
     */
    public function test_add_lot($currencyId, $sellerId, $dateTimeOpen, $dateTimeClose, $price)
    {
        $request = new AddLotRequest($currencyId, $sellerId, $dateTimeOpen, $dateTimeClose, $price);

        $lot = $this->marketService->addLot($request);

        $this->assertInstanceOf(Lot::class,$lot);
        $this->assertEquals($currencyId,$lot->currency_id);
        $this->assertEquals($sellerId,$lot->seller_id);
        $this->assertEquals($dateTimeOpen,$lot->date_time_open);
        $this->assertEquals($dateTimeClose,$lot->date_time_close);
        $this->assertEquals($price,$lot->price);
    }

    /**
     * @dataProvider addLotDataProvider
     */
    public function test_add_lot_when_user_has_active($currencyId, $sellerId, $dateTimeOpen, $dateTimeClose, $price)
    {

        $this->expectException(IncorrectLotAmountException::class);
        $request = new AddLotRequest($currencyId, $sellerId, $dateTimeOpen, $dateTimeClose, $price);
        $lot = $this->marketService->addLot($request);

        $this->lotRepository->method('findActiveLots')->willReturn([$lot]);
        $this->marketService->addLot($request);
    }


    /**
     * @dataProvider addLotDataProvider
     */
    public function test_add_lot_negative_price($currencyId, $sellerId, $dateTimeOpen, $dateTimeClose)
    {
        $price = -1;
        $this->expectException(IncorrectPriceException::class);
        $request = new AddLotRequest($currencyId, $sellerId, $dateTimeOpen, $dateTimeClose, $price);
        $this->marketService->addLot($request);
    }

    /**
     * @dataProvider addLotDataProvider
     */
    /*public function test_buy_lot($currencyId, $sellerId, $dateTimeOpen, $dateTimeClose, $price)
    {
        $userId = 1;
        $lotId = 1;
        $amount = 1;

        $request = new BuyLotRequest($userId, $lotId, $amount);
        $lot = factory(Lot::class)->make(['date_time_open'=>$dateTimeOpen,'date_time_close'=>$dateTimeClose]);

        $this->lotRepository->method('findActiveLots')->willReturn([$lot]);

        $this->lotRepository->method('getById')->willReturn($lot);

        $trade = $this->marketService->buyLot($request);

        $this->assertInstanceOf(Trade::class,$trade);
        $this->assertEquals($userId,$trade->user_id);
        $this->assertEquals($lotId,$trade->lot_id);
        $this->assertEquals($amount,$trade->amount);
    }*/


    public function addLotDataProvider()
    {
        return [
            [1,1,Carbon::now()->addHour(-1)->timestamp,Carbon::now()->addHour(1)->timestamp,1],
        ];
    }
}
