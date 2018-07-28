<?php

namespace Tests\Feature;

use App\Entity\Currency;
use App\Entity\Lot;
use App\Entity\Money;
use App\Entity\Wallet;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetLotsTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_lots_valid()
    {
        $currency = factory(Currency::class)->create();
        $sellers = factory(User::class,20)->create();
        foreach($sellers as $seller){
            $wallet = factory(Wallet::class)->create([
                'user_id' => $seller->id
            ]);
            factory(Money::class)->create([
                'wallet_id' => $wallet->id,
                'currency_id' => $currency->id,
                'amount' => random_int(200,5000)
            ]);
        }
        $user = $sellers[0];
        $sellersIds = $sellers->pluck('id')->toArray();
        $lots = factory(Lot::class,10)->create([
            'seller_id'=>array_random($sellersIds),
            'currency_id'=>$currency->id
        ]);

        $lotsResponseArray = [];
        foreach($lots as $lot){
            $lotsResponse = [];
            $lotsResponse['id'] = $lot->id;
            $lotsResponse['currency'] = $currency->name;
            $lotsResponse['user'] = $lot->seller->name;
            $lotsResponse['amount'] = Money::where([
                'currency_id'=>$currency->id,
                'wallet_id'=>$lot->seller->wallet->id
            ])->first()->amount;
            $lotsResponse['price'] = number_format($lot->price,2,',','');
            $lotsResponse['date_time_open'] = str_replace('-','/',$lot->date_time_open);
            $lotsResponse['date_time_close'] = str_replace('-','/',$lot->date_time_close);
            $lotsResponseArray[] = $lotsResponse;
        }

        $response = $this->actingAs($user)
            ->json('GET','/api/v1/lots');
        $response
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson(
                $lotsResponseArray
            );
    }


    public function test_get_lots_unauthenticated()
    {
        $response = $this->json('GET','/api/v1/lots');

        $response
            ->assertStatus(403)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson(
                [
                    "error" => [
                        "message" => "Unauthenticated.",
                        "code" => 403
                    ]
                ]
            );
    }
}
