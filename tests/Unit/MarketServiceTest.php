<?php

namespace Tests\Unit;

use App\Entity\Currency;
use App\Entity\Lot;
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

        //factory(Currency::class)->make();
        $lot = $this->marketService->addLot($request);
        $this->assertInstanceOf(Lot::class,$lot);
        $this->assertEquals($currencyId,$lot->currency_id);
        $this->assertEquals($sellerId,$lot->seller_id);
        $this->assertEquals($dateTimeOpen,$lot->date_time_open);
        $this->assertEquals($dateTimeClose,$lot->dateTimeClose);
        $this->assertEquals($price,$lot->price);
    }

   /* public function getAddLotRequestMock($currencyId, $sellerId, $dateTimeOpen, $dateTimeClose, $price)
    {

        $request = $this->createMock(AddLotRequest::class);
        $request->method('getCurrencyId')->willReturn($currencyId);
        $request->method('getSellerId')->willReturn($sellerId);
        $request->method('getDateTimeOpen')->willReturn($dateTimeOpen);
        $request->method('getDateTimeClose')->willReturn($dateTimeClose);
        $request->method('getPrice')->willReturn($price);

        return $request;
    }*/
    public function addLotDataProvider()
    {
        return [
            [1,1,0,1,1],
        ];
    }
}
