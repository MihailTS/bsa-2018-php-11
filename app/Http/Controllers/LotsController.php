<?php

namespace App\Http\Controllers;

use App\Http\Requests\Lots\StoreLotRequest;
use App\Request\AddLotRequest;
use App\Service\Contracts\MarketService;
use Auth;
use Illuminate\Http\Request;

class LotsController extends Controller
{

    private $marketService;

    public function __construct(MarketService $marketService)
    {
        $this->middleware('auth');
        $this->marketService = $marketService;
    }

    public function add()
    {
        return view('lots.add');
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
            Auth::user()->id,
            $request->getDateTimeOpen(),
            $request->getDateTimeClose(),
            $request->getPrice()
        );
        try {
            $this->marketService->addLot($addLotRequest);
            $params = ['success'=>true];
        }catch(\LogicException $e){
            $errorMsg = $e->getMessage();
            $params = [
                'error' => true,
                'errorMsg' => $errorMsg
            ];
        };
        return view('lots.add',$params);
    }

}
