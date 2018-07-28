<?php

namespace Tests\Feature;

use App\Entity\Currency;
use App\Entity\Lot;
use App\Entity\Money;
use App\Entity\Wallet;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BuyLotTest extends TestCase
{
    use RefreshDatabase;

    public function test_buy_lot_valid()
    {
        $amount = 99.99;
        $seller = factory(User::class)->create();
        $buyer = factory(User::class)->create();
        $currency = factory(Currency::class)->create();
        $sellerWallet = factory(Wallet::class)->create([
            'user_id' => $seller->id
        ]);
        $sellerMoney = factory(Money::class)->create([
           'wallet_id' => $sellerWallet->id,
           'currency_id' => $currency->id,
           'amount' => $amount+200
        ]);
        $buyerWallet = factory(Wallet::class)->create([
            'user_id' => $buyer->id
        ]);
        $buyerMoney = factory(Money::class)->create([
            'wallet_id' => $buyerWallet->id,
            'currency_id' => $currency->id,
        ]);
        $lot = factory(Lot::class)->create([
            'seller_id'=>$seller->id,
            'currency_id'=>$currency->id
        ]);

        $response = $this->actingAs($buyer)
            ->json('POST','/api/v1/trades',[
                'lot_id' => $lot->id,
                'amount' => $amount,
            ]);

        $response
            ->assertStatus(201)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson(
                [
                    "lot_id" => $lot->id,
                    "user_id" => $buyer->id,
                    "amount" => $amount,
                ]
            );
    }


    public function test_buy_lot_not_enough_money()
    {
        $amount = 99.99;
        $seller = factory(User::class)->create();
        $buyer = factory(User::class)->create();
        $currency = factory(Currency::class)->create();
        $sellerWallet = factory(Wallet::class)->create([
            'user_id' => $seller->id
        ]);
        $sellerMoney = factory(Money::class)->create([
            'wallet_id' => $sellerWallet->id,
            'currency_id' => $currency->id,
            'amount' => $amount-200
        ]);
        $buyerWallet = factory(Wallet::class)->create([
            'user_id' => $buyer->id
        ]);
        $buyerMoney = factory(Money::class)->create([
            'wallet_id' => $buyerWallet->id,
            'currency_id' => $currency->id,
        ]);
        $lot = factory(Lot::class)->create([
            'seller_id'=>$seller->id,
            'currency_id'=>$currency->id
        ]);

        $response = $this->actingAs($buyer)
            ->json('POST','/api/v1/trades',[
                'lot_id' => $lot->id,
                'amount' => $amount,
            ]);

        $response
            ->assertStatus(400)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson(
                [
                    "error" => [
                        "message" => "Not enough money in lot for this operation",
                        "code" => 400
                    ]
                ]
            );
    }


    public function test_buy_lot_own_lot()
    {
        $amount = 99.99;
        $seller = factory(User::class)->create();
        $buyer = $seller;
        $currency = factory(Currency::class)->create();
        $sellerWallet = factory(Wallet::class)->create([
            'user_id' => $seller->id
        ]);
        $sellerMoney = factory(Money::class)->create([
            'wallet_id' => $sellerWallet->id,
            'currency_id' => $currency->id,
            'amount' => $amount+200
        ]);

        $lot = factory(Lot::class)->create([
            'seller_id'=>$seller->id,
            'currency_id'=>$currency->id
        ]);

        $response = $this->actingAs($buyer)
            ->json('POST','/api/v1/trades',[
                'lot_id' => $lot->id,
                'amount' => $amount,
            ]);

        $response
            ->assertStatus(400)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson(
                [
                    "error" => [
                        "message" => "User can't buy own currency",
                        "code" => 400
                    ]
                ]
            );
    }


    public function test_buy_lot_inactive_lot()
    {
        $amount = 99.99;
        $seller = factory(User::class)->create();
        $buyer = factory(User::class)->create();
        $currency = factory(Currency::class)->create();
        $sellerWallet = factory(Wallet::class)->create([
            'user_id' => $seller->id
        ]);
        $sellerMoney = factory(Money::class)->create([
            'wallet_id' => $sellerWallet->id,
            'currency_id' => $currency->id,
            'amount' => $amount+200
        ]);
        $buyerWallet = factory(Wallet::class)->create([
            'user_id' => $buyer->id
        ]);
        $buyerMoney = factory(Money::class)->create([
            'wallet_id' => $buyerWallet->id,
            'currency_id' => $currency->id,
        ]);
        $lot = factory(Lot::class)->create([
            'seller_id'=>$seller->id,
            'currency_id'=>$currency->id,
            'date_time_open'=>now()->timestamp-500,
            'date_time_close'=>now()->timestamp-200
        ]);

        $response = $this->actingAs($buyer)
            ->json('POST','/api/v1/trades',[
                'lot_id' => $lot->id,
                'amount' => $amount,
            ]);

        $response
            ->assertStatus(400)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson(
                [
                    "error" => [
                        "message" => "Lot $lot->id isn't active",
                        "code" => 400
                    ]
                ]
            );
    }
}
