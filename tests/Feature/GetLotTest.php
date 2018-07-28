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

class GetLotTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_lot_valid()
    {
        $currency = factory(Currency::class)->create();
        $seller = factory(User::class)->create();
        $wallet = factory(Wallet::class)->create([
            'user_id' => $seller->id
        ]);
        factory(Money::class)->create([
            'wallet_id' => $wallet->id,
            'currency_id' => $currency->id,
            'amount' => random_int(200,5000)
        ]);
        $lot = factory(Lot::class)->create([
            'seller_id'=>$seller->id,
            'currency_id'=>$currency->id
        ]);

        $lotsResponse = [];
        $lotsResponse['id'] = $lot->id;
        $lotsResponse['currency_name'] = $currency->name;
        $lotsResponse['user_name'] = $seller->name;
        $lotsResponse['amount'] = Money::where([
            'currency_id'=>$currency->id,
            'wallet_id'=>$seller->wallet->id
        ])->first()->amount;
        $lotsResponse['price'] = number_format($lot->price,2,',','');
        $lotsResponse['date_time_open'] = str_replace('-','/',$lot->date_time_open);
        $lotsResponse['date_time_close'] = str_replace('-','/',$lot->date_time_close);


        $response = $this->actingAs($seller)
            ->json('GET',"/api/v1/lots/$lot->id");
        $response
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson(
                $lotsResponse
            );
    }


    public function test_get_lot_unauthenticated()
    {
        $response = $this->json('GET',"/api/v1/lots/1");

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
