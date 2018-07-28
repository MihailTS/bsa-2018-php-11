<?php

namespace Tests\Unit;


use App\Entity\Lot;
use App\Entity\Money;
use App\Entity\Wallet;

use App\Exceptions\MarketException\BuyInactiveLotException;
use App\Exceptions\MarketException\BuyNegativeAmountException;
use App\Exceptions\MarketException\BuyOwnCurrencyException;
use App\Exceptions\MarketException\IncorrectLotAmountException;
use App\Exceptions\MarketException\LotDoesNotExistException;
use App\Repository\Contracts\LotRepository;
use App\Repository\Contracts\MoneyRepository;
use App\Repository\Contracts\UserRepository;
use App\Repository\Contracts\WalletRepository;
use App\Request\BuyLotRequest;
use App\Validators\Market\BuyLotValidator;
use Carbon\Carbon;
use Tests\TestCase;

class BuyLotValidatorTest extends TestCase
{
    private $lotRepository;
    private $buyLotValidator;
    private $userRepository;
    private $walletRepository;
    private $moneyRepository;

    protected function setUp()
    {
        parent::setUp();

        $this->lotRepository = $this->createMock(LotRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->walletRepository = $this->createMock(WalletRepository::class);
        $this->moneyRepository = $this->createMock(MoneyRepository::class);

        $this->buyLotValidator = new BuyLotValidator(
            $this->userRepository,
            $this->moneyRepository,
            $this->walletRepository,
            $this->lotRepository
        );

    }

    public function test_validate_valid_returns_true()
    {
        $userId = 1;
        $lotId = 1;
        $buyAmount = 99.99;

        $validDateTimeOpen = Carbon::now()->addHour(-1)->timestamp;
        $validDateTimeClose = Carbon::now()->addHour(1)->timestamp;

        $request = new BuyLotRequest(
            $userId,
            $lotId,
            $buyAmount
        );
        $this->lotRepository->method('getById')->willReturn(factory(Lot::class)->make([
            'currency_id' => 1,
            'date_time_open' => $validDateTimeOpen,
            'date_time_close' => $validDateTimeClose
        ]));
        $this->moneyRepository->method('findByWalletAndCurrency')->willReturn(factory(Money::class)->make(
            ['amount' => $buyAmount + 100]
        ));
        $this->walletRepository->method('findByUser')->willReturn(factory(Wallet::class)->make(['id' => 1]));

        $success = $this->buyLotValidator->validate($request);

        $this->assertTrue($success);
    }


    public function test_validate_negative_amount()
    {
        $this->expectException(BuyNegativeAmountException::class);

        $userId = 1;
        $lotId = 1;

        $validDateTimeOpen = Carbon::now()->addHour(-1)->timestamp;
        $validDateTimeClose = Carbon::now()->addHour(1)->timestamp;

        $request = new BuyLotRequest(
            $userId,
            $lotId,
            -1
        );
        $this->lotRepository->method('getById')->willReturn(factory(Lot::class)->make([
            'currency_id' => 1,
            'date_time_open' => $validDateTimeOpen,
            'date_time_close' => $validDateTimeClose
        ]));
        $this->moneyRepository->method('findByWalletAndCurrency')->willReturn(factory(Money::class)->make());
        $this->walletRepository->method('findByUser')->willReturn(factory(Wallet::class)->make(['id' => 1]));

        $this->buyLotValidator->validate($request);
    }

    public function test_validate_incorrect_amount()
    {
        $this->expectException(IncorrectLotAmountException::class);

        $userId = 1;
        $lotId = 1;
        $buyAmount = 0;

        $validDateTimeOpen = Carbon::now()->addHour(-1)->timestamp;
        $validDateTimeClose = Carbon::now()->addHour(1)->timestamp;

        $request = new BuyLotRequest(
            $userId,
            $lotId,
            $buyAmount
        );
        $this->lotRepository->method('getById')->willReturn(factory(Lot::class)->make([
            'currency_id' => 1,
            'date_time_open' => $validDateTimeOpen,
            'date_time_close' => $validDateTimeClose
        ]));
        $this->moneyRepository->method('findByWalletAndCurrency')->willReturn(factory(Money::class)->make(
            ['amount' => $buyAmount + 100]
        ));
        $this->walletRepository->method('findByUser')->willReturn(factory(Wallet::class)->make(['id' => 1]));

        $this->buyLotValidator->validate($request);
    }

    public function test_validate_buy_own_currency()
    {
        $this->expectException(BuyOwnCurrencyException::class);

        $userId = 1;
        $lotId = 1;
        $sellerId = 1;
        $buyAmount = 99.99;

        $validDateTimeOpen = Carbon::now()->addHour(-1)->timestamp;
        $validDateTimeClose = Carbon::now()->addHour(1)->timestamp;

        $request = new BuyLotRequest(
            $userId,
            $lotId,
            $buyAmount
        );
        $this->lotRepository->method('getById')->willReturn(factory(Lot::class)->make([
            'currency_id' => 1,
            'seller_id' => $sellerId,
            'date_time_open' => $validDateTimeOpen,
            'date_time_close' => $validDateTimeClose
        ]));
        $this->moneyRepository->method('findByWalletAndCurrency')->willReturn(factory(Money::class)->make(
            ['amount' => $buyAmount + 100]
        ));
        $this->walletRepository->method('findByUser')->willReturn(factory(Wallet::class)->make(['id' => 1]));

        $this->buyLotValidator->validate($request);
    }


    public function test_validate_buy_inactive_lot()
    {
        $this->expectException(BuyInactiveLotException::class);

        $userId = 1;
        $lotId = 1;
        $sellerId = 2;
        $buyAmount = 99.99;

        $validDateTimeOpen = Carbon::now()->addHour(-1)->timestamp;
        $validDateTimeClose = Carbon::now()->addHour(-1)->timestamp;

        $request = new BuyLotRequest(
            $userId,
            $lotId,
            $buyAmount
        );
        $this->lotRepository->method('getById')->willReturn(factory(Lot::class)->make([
            'currency_id' => 1,
            'seller_id' => $sellerId,
            'date_time_open' => $validDateTimeOpen,
            'date_time_close' => $validDateTimeClose
        ]));
        $this->moneyRepository->method('findByWalletAndCurrency')->willReturn(factory(Money::class)->make(
            ['amount' => $buyAmount + 100]
        ));
        $this->walletRepository->method('findByUser')->willReturn(factory(Wallet::class)->make(['id' => 1]));

        $this->buyLotValidator->validate($request);
    }


    public function test_validate_not_enough_money()
    {
        $this->expectException(IncorrectLotAmountException::class);

        $userId = 1;
        $lotId = 1;
        $sellerId = 2;
        $buyAmount = 99.00;

        $validDateTimeOpen = Carbon::now()->addHour(-1)->timestamp;
        $validDateTimeClose = Carbon::now()->addHour(1)->timestamp;

        $request = new BuyLotRequest(
            $userId,
            $lotId,
            $buyAmount
        );
        $this->lotRepository->method('getById')->willReturn(factory(Lot::class)->make([
            'currency_id' => 1,
            'seller_id' => $sellerId,
            'date_time_open' => $validDateTimeOpen,
            'date_time_close' => $validDateTimeClose
        ]));

        $this->moneyRepository->method('findByWalletAndCurrency')->willReturn(factory(Money::class)->make(
            ['amount' => 0]
        ));
        $this->walletRepository->method('findByUser')->willReturn(factory(Wallet::class)->make(['id' => 1]));

        $this->buyLotValidator->validate($request);
    }


    public function test_validate_lot_does_not_exists()
    {
        $this->expectException(LotDoesNotExistException::class);

        $userId = 1;
        $lotId = 1;
        $buyAmount = 99.00;

        $request = new BuyLotRequest(
            $userId,
            $lotId,
            $buyAmount
        );
        $this->lotRepository->method('getById')->willReturn(null);

        $this->buyLotValidator->validate($request);
    }
}
