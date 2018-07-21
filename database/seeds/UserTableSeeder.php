<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Entity\User;

class UserTableSeeder extends Seeder
{
    private const ITEMS_COUNT = 30;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class,self::ITEMS_COUNT)->create();
    }
}
