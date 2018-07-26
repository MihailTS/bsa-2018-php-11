<?php

namespace Tests\Unit;


use App\Entity\Lot;
use App\Exceptions\MarketException\ActiveLotExistsException;
use App\Exceptions\MarketException\IncorrectPriceException;
use App\Exceptions\MarketException\IncorrectTimeCloseException;
use App\Repository\Contracts\LotRepository;
use App\Request\AddLotRequest;
use App\Validators\Market\AddLotValidator;
use Carbon\Carbon;
use Tests\TestCase;

class AddLotValidatorTest extends TestCase
{
    private $lotRepository;
    private $addLotValidator;

    protected function setUp()
    {
        parent::setUp();

        $this->lotRepository = $this->createMock(LotRepository::class);

        $this->addLotValidator = new AddLotValidator($this->lotRepository);
        $this->lotRepository->method('add')->will($this->returnArgument(0));

    }

    public function test_validate_valid_returns_true()
    {
        $validDateTimeOpen = Carbon::now()->addHour(-1)->timestamp;
        $validDateTimeClose = Carbon::now()->addHour(1)->timestamp;
        $currencyId = 1;
        $sellerId = 1;

        $request = new AddLotRequest(
            $currencyId,
            $sellerId,
            $validDateTimeOpen,
            $validDateTimeClose,
            99.99
        );
        $lotWithAnotherCurrency = factory(Lot::class)->make([
            'seller_id'=>$sellerId,
            'currency_id' => $currencyId+1,
            'date_time_open'=>$validDateTimeOpen,
            'date_time_close'=>$validDateTimeClose,
        ]);
        $this->lotRepository->method('findActiveLots')->willReturn([
            $lotWithAnotherCurrency
        ]);
        $success = $this->addLotValidator->validate($request);

        $this->assertTrue($success);
    }


    public function test_validate_already_has_active_lots()
    {
        $this->expectException(ActiveLotExistsException::class);

        $validDateTimeOpen = Carbon::now()->addHour(-1)->timestamp;
        $validDateTimeClose = Carbon::now()->addHour(1)->timestamp;
        $currencyId = 1;
        $sellerId = 1;

        $request = new AddLotRequest(
            $currencyId,
            $sellerId,
            $validDateTimeOpen,
            $validDateTimeClose,
            99.99
        );
        $lotWithSameCurrency = factory(Lot::class)->make([
            'seller_id'=>$sellerId,
            'currency_id' => $currencyId,
            'date_time_open'=>$validDateTimeOpen,
            'date_time_close'=>$validDateTimeClose,
        ]);
        $this->lotRepository->method('findActiveLots')->willReturn([
            $lotWithSameCurrency
        ]);

        $this->addLotValidator->validate($request);
    }

    public function test_validate_negative_price()
    {
        $this->expectException(IncorrectPriceException::class);

        $validDateTimeOpen = Carbon::now()->addHour(-1)->timestamp;
        $validDateTimeClose = Carbon::now()->addHour(1)->timestamp;
        $currencyId = 1;
        $sellerId = 1;

        $request = new AddLotRequest(
            $currencyId,
            $sellerId,
            $validDateTimeOpen,
            $validDateTimeClose,
            -1
        );
        $this->lotRepository->method('findActiveLots')->willReturn([]);

        $this->addLotValidator->validate($request);
    }


    public function test_validate_time_close_before_open()
    {
        $this->expectException(IncorrectTimeCloseException::class);

        $validDateTimeOpen = Carbon::now()->addHour(-1)->timestamp;
        $validDateTimeClose = Carbon::now()->addHour(-2)->timestamp;
        $currencyId = 1;
        $sellerId = 1;

        $request = new AddLotRequest(
            $currencyId,
            $sellerId,
            $validDateTimeOpen,
            $validDateTimeClose,
            99.99
        );
        $this->lotRepository->method('findActiveLots')->willReturn([]);

        $this->addLotValidator->validate($request);
    }
}
