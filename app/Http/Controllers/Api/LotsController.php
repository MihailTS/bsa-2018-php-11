<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Lots\StoreLotRequest;
use App\Request\AddLotRequest;
use App\Response\LotResponse;
use App\Service\Contracts\MarketService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LotsController extends Controller
{

    private $marketService;

    public function __construct(MarketService $marketService)
    {
        $this->marketService = $marketService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lotsResponses = $this->marketService->getLotList();
        $lots = array_map(
            function($lotResponse){
               return LotResponse::toArray($lotResponse);
            },
            $lotsResponses
        );
        return response($lots);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param StoreLotRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLotRequest $request)
    {
        $addLotRequest = new AddLotRequest(
            $request->getCurrencyId(),
            $request->getSellerId(),
            $request->getDateTimeOpen(),
            $request->getDateTimeClose(),
            $request->getPrice()
        );
        $lot = $this->marketService->addLot($addLotRequest);
        return response($lot,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $lotResponse = $this->marketService->getLot($id);
        $lot = LotResponse::toArray($lotResponse);
        return response($lot);
    }


}
