<?php

namespace Tests\Feature;

use App\Entity\Currency;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddLotTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_lot_valid()
    {
        $user = factory(User::class)->create();
        $currency = factory(Currency::class)->create();
        $validDateTimeOpen = Carbon::now()->addHour(-1);
        $validDateTimeClose = Carbon::now()->addHour(1);
        $price = 99.99;

        $response = $this->actingAs($user)
            ->json('POST','/api/v1/lots',[
                'currency_id' => $currency->id,
                'date_time_open' => $validDateTimeOpen->timestamp,
                'date_time_close' => $validDateTimeClose->timestamp,
                'price' => $price
            ]);

        $response
            ->assertStatus(201)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson(
                [
                    "currency_id"=> $currency->id,
                    "seller_id"=> $user->id,
                    "price"=> $price,
                    "date_time_open"=> $validDateTimeOpen,
                    "date_time_close" => $validDateTimeClose
                ]
            );
    }

    public function test_add_lot_unauthenticated()
    {
        $currency = factory(Currency::class)->create();
        $validDateTimeOpen = Carbon::now()->addHour(-1);
        $validDateTimeClose = Carbon::now()->addHour(1);
        $price = 99.99;

        $response = $this->json('POST','/api/v1/lots',[
                'currency_id' => $currency->id,
                'date_time_open' => $validDateTimeOpen->timestamp,
                'date_time_close' => $validDateTimeClose->timestamp,
                'price' => $price
            ]);
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

    public function test_add_lot_has_active_currency()
    {
        $user = factory(User::class)->create();
        $currency = factory(Currency::class)->create();
        $validDateTimeOpen = Carbon::now()->addHour(-1);
        $validDateTimeClose = Carbon::now()->addHour(1);
        $price = 99.99;

       $this->actingAs($user)
            ->json('POST','/api/v1/lots',[
                'currency_id' => $currency->id,
                'date_time_open' => $validDateTimeOpen->timestamp,
                'date_time_close' => $validDateTimeClose->timestamp,
                'price' => $price
            ]);
        $response = $this->actingAs($user)
            ->json('POST','/api/v1/lots',[
                'currency_id' => $currency->id,
                'date_time_open' => $validDateTimeOpen->timestamp,
                'date_time_close' => $validDateTimeClose->timestamp,
                'price' => $price
            ]);

        $response
            ->assertStatus(400)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson(
                [
                    "error" => [
                        "message" => "User already has active currency lot",
                        "code" => 400
                    ]
                ]
            );
    }

    public function test_add_close_time_before_open()
    {
        $user = factory(User::class)->create();
        $currency = factory(Currency::class)->create();
        $validDateTimeOpen = Carbon::now()->addHour(-1);
        $validDateTimeClose = Carbon::now()->addHour(-2);
        $price = 99.99;

        $response = $this->actingAs($user)
            ->json('POST','/api/v1/lots',[
                'currency_id' => $currency->id,
                'date_time_open' => $validDateTimeOpen->timestamp,
                'date_time_close' => $validDateTimeClose->timestamp,
                'price' => $price
            ]);

        $response
            ->assertStatus(400)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson(
                [
                    "error" => [
                        "message" => "Close datetime can't be before open",
                        "code" => 400
                    ]
                ]
            );
    }

    public function test_add_incorrect_price()
    {
        $user = factory(User::class)->create();
        $currency = factory(Currency::class)->create();
        $validDateTimeOpen = Carbon::now()->addHour(-1);
        $validDateTimeClose = Carbon::now()->addHour(1);
        $price = -1;

        $response = $this->actingAs($user)
            ->json('POST','/api/v1/lots',[
                'currency_id' => $currency->id,
                'date_time_open' => $validDateTimeOpen->timestamp,
                'date_time_close' => $validDateTimeClose->timestamp,
                'price' => $price
            ]);

        $response
            ->assertStatus(400)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson(
                [
                    "error" => [
                        "message" => "Price must be positive",
                        "code" => 400
                    ]
                ]
            );
    }
}
