<?php

namespace App\Providers;

use App\Validators\UserHasEnoughMoney;
use App\Validators\UserHasNotActiveLotsWithCurrency;
use App\Validators\UserHasNotWallet;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;


use App\Repository\Contracts\CurrencyRepository as CurrencyRepositoryContract;
use App\Repository\CurrencyRepository;
use App\Repository\Contracts\LotRepository as LotRepositoryContract;
use App\Repository\LotRepository;
use App\Repository\Contracts\MoneyRepository as MoneyRepositoryContract;
use App\Repository\MoneyRepository;
use App\Repository\Contracts\TradeRepository as TradeRepositoryContract;
use App\Repository\TradeRepository;
use App\Repository\Contracts\UserRepository as UserRepositoryContract;
use App\Repository\UserRepository;
use App\Repository\Contracts\WalletRepository as WalletRepositoryContract;
use App\Repository\WalletRepository;

use App\Request\AddLotRequest;
use App\Request\Contracts\AddLotRequest as AddLotRequestContract;
use App\Request\BuyLotRequest;
use App\Request\Contracts\BuyLotRequest as BuyLotRequestContract;
use App\Request\CreateWalletRequest;
use App\Request\Contracts\CreateWalletRequest as CreateWalletRequestContract;
use App\Request\AddCurrencyRequest;
use App\Request\Contracts\AddCurrencyRequest as AddCurrencyRequestContract;
use App\Request\MoneyRequest;
use App\Request\Contracts\MoneyRequest as MoneyRequestContract;

use App\Service\Contracts\CurrencyService as CurrencyServiceContract;
use App\Service\CurrencyService;
use App\Service\Contracts\MarketService as MarketServiceContract;
use App\Service\MarketService;
use App\Service\Contracts\WalletService as WalletServiceContract;
use App\Service\WalletService;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind(CurrencyRepositoryContract::class, CurrencyRepository::class);
        $this->app->bind(LotRepositoryContract::class, LotRepository::class);
        $this->app->bind(MoneyRepositoryContract::class, MoneyRepository::class);
        $this->app->bind(TradeRepositoryContract::class, TradeRepository::class);
        $this->app->bind(UserRepositoryContract::class, UserRepository::class);
        $this->app->bind(WalletRepositoryContract::class, WalletRepository::class);

        $this->app->bind(AddLotRequestContract::class,AddLotRequest::class);
        $this->app->bind(BuyLotRequestContract::class,BuyLotRequest::class);
        $this->app->bind(CreateWalletRequestContract::class,CreateWalletRequest::class);
        $this->app->bind(AddCurrencyRequestContract::class,AddCurrencyRequest::class);
        $this->app->bind(MoneyRequestContract::class,MoneyRequest::class);

        $this->app->bind(CurrencyServiceContract::class, CurrencyService::class);
        $this->app->bind(MarketServiceContract::class, MarketService::class);
        $this->app->bind(WalletServiceContract::class, WalletService::class);
    }
}
