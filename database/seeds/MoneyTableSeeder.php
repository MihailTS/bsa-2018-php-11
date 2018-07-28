<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Entity\Money;
use App\Entity\Wallet;
use App\Entity\Currency;

class MoneyTableSeeder extends Seeder
{

    private const ITEMS_COUNT = 10;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $currencies = Currency::all()->take(20)->pluck('id')->toArray();
        $wallets = Wallet::all()->take(20)->pluck('id')->toArray();

        for ($i = 0; $i < self::ITEMS_COUNT; $i++) {
            $currencyId = $faker->randomElement($currencies);
            $walletId = $faker->randomElement($wallets);
            factory(Money::class)->create([
                'currency_id' => $currencyId,
                'wallet_id' => $walletId
            ]);
        }
    }
}
