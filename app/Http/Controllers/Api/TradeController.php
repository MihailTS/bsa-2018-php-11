<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Trades\StoreTradeRequest;
use App\Request\BuyLotRequest;
use App\Service\Contracts\MarketService;
use App\Http\Controllers\Controller;
use Auth;

class TradeController extends Controller
{

    private $marketService;

    public function __construct(MarketService $marketService)
    {
        $this->middleware('auth');

        $this->marketService = $marketService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTradeRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTradeRequest $request)
    {
        $buyLotRequest = new BuyLotRequest(
            Auth::user()->id,
            $request->getLotId(),
            $request->getAmount()
        );
        $trade = $this->marketService->buyLot($buyLotRequest);
        return response($trade,201);
    }


}
