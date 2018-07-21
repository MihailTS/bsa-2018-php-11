<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Entity\Wallet;
use App\Entity\User;

class WalletTableSeeder extends Seeder
{

    private const ITEMS_COUNT = 20;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all()->take(self::ITEMS_COUNT)->pluck('id')->toArray();
        foreach($users as $userId){
            factory(Wallet::class)->create(['user_id' => $userId]);
        }
    }
}
