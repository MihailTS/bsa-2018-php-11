<?php

namespace Tests\Unit;

use App\Entity\Currency;
use App\Entity\Lot;
use App\Entity\Money;
use App\Entity\Wallet;
use App\Mail\TradeCreated;
use App\Service\Contracts\WalletService;
use App\User;
use App\Validators\Market\AddLotValidator;
use App\Validators\Market\BuyLotValidator;
use Mail;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repository\Contracts\CurrencyRepository;
use App\Repository\Contracts\LotRepository;
use App\Repository\Contracts\MoneyRepository;
use App\Repository\Contracts\TradeRepository;
use App\Repository\Contracts\UserRepository;
use App\Repository\Contracts\WalletRepository;
use App\Service\MarketService;
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
    private $walletService;
    private $addLotValidator;
    private $buyLotValidator;

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

        $this->walletService = $this->createMock(WalletService::class);

        $this->addLotValidator = $this->createMock(AddLotValidator::class);
        $this->buyLotValidator = $this->createMock(BuyLotValidator::class);

        $this->marketService = new MarketService(
            $this->lotRepository,
            $this->tradeRepository,
            $this->walletRepository,
            $this->userRepository,
            $this->currencyRepository,
            $this->moneyRepository,
            $this->walletService,
            $this->addLotValidator,
            $this->buyLotValidator
        );

    }

    public function test_add_lot()
    {
        $currencyId = 1;
        $sellerId = 1;
        $dateTimeOpen = Carbon::now()->addHour(-1)->timestamp;
        $dateTimeClose = Carbon::now()->addHour(1)->timestamp;
        $price = 99.99;

        $this->addLotValidator->expects($this->once())->method('validate');
        $request = new AddLotRequest($currencyId, $sellerId, $dateTimeOpen, $dateTimeClose, $price);

        $lot = $this->marketService->addLot($request);

        $this->assertEquals($currencyId, $lot->currency_id);
        $this->assertEquals($sellerId, $lot->seller_id);
        $this->assertEquals($dateTimeOpen, $lot->date_time_open->timestamp);
        $this->assertEquals($dateTimeClose, $lot->date_time_close->timestamp);
        $this->assertEquals($price, $lot->price);
    }

    public function test_buy_lot()
    {
        Mail::fake();

        $userId = 1;
        $lotId = 1;
        $amount = 99.99;

        $this->buyLotValidator->expects($this->once())->method('validate');
        $buyer = factory(User::class)->make(['id' => $userId]);
        $lot = factory(Lot::class)->make(['id' => $lotId]);
        $wallet = factory(Wallet::class)->make();
        $currency = factory(Currency::class)->make();

        $this->userRepository->method('getById')->willReturn($buyer);
        $this->lotRepository->method('getById')->willReturn($lot);
        $this->walletRepository->method('findByUser')->willReturn($wallet);
        $this->currencyRepository->method('getById')->willReturn($currency);
        $this->moneyRepository->method('findByWalletAndCurrency')->willReturn(
            factory(Money::class)->make(['wallet_id' => 1, 'currency_id' => 1])
        );

        $request = new BuyLotRequest($userId, $lotId, $amount);
        $trade = $this->marketService->buyLot($request);

        $this->assertEquals($userId, $trade->user_id);
        $this->assertEquals($lotId, $trade->lot_id);
        $this->assertEquals($amount, $trade->amount);

        Mail::assertSent(TradeCreated::class);
    }

    public function test_get_lot()
    {
        $lot = factory(Lot::class)->make(['id' => 1]);
        $user = factory(User::class)->make();
        $currency = factory(Currency::class)->make();
        $wallet = factory(Wallet::class)->make(['id' => 1]);
        $money = factory(Money::class)->make();

        $this->lotRepository->method('getById')->willReturn($lot);
        $this->userRepository->method('getById')->willReturn($user);
        $this->currencyRepository->method('getById')->willReturn($currency);
        $this->walletRepository->method('findByUser')->willReturn($wallet);
        $this->moneyRepository->method('findByWalletAndCurrency')->willReturn($money);

        $lotResponse = $this->marketService->getLot(1);
        $this->assertEquals($lotResponse->getId(), $lot->id);
        $this->assertEquals($lotResponse->getUserName(), $user->name);
        $this->assertEquals($lotResponse->getCurrencyName(), $currency->name);
        $this->assertEquals($lotResponse->getAmount(), $money->amount);
        $this->assertEquals(
            $lotResponse->getDateTimeOpen(),
            $lot->date_time_open->format('Y/m/d h:i:s')
        );
        $this->assertEquals(
            $lotResponse->getDateTimeClose(),
            $lot->date_time_close->format('Y/m/d h:i:s')
        );
        $this->assertEquals(
            $lotResponse->getPrice(),
            number_format($lot->price, 2, ',', '')
        );
    }

    public function test_get_lot_list()
    {
        $lots = [];
        for ($i = 0; $i < 5; $i++) {
            $lots[] = factory(Lot::class)->make(['id'=>$i]);
        }
        $this->lotRepository->method('findAll')->willReturn($lots);

        $lotsFromService = $this->marketService->getLotList();
        foreach ($lots as $key => $lot) {
            $lotResponse = $lotsFromService[$key];
            $this->assertEquals($lotResponse->getId(), $lot->id);
        }
    }
}
