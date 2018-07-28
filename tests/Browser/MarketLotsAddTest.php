<?php
namespace Tests\Browser;

use App\Entity\Currency;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Carbon\Carbon;
use App\Entity\Lot;

class MarketLotsAddTest extends DuskTestCase
{
    use DatabaseMigrations;

    private $url = '/market/lots/add';

    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function test_add_lot_valid()
    {
        $user = factory(User::class)->create();
        $currency = factory(Currency::class)->create();

        $this->browse(function(Browser $browser) use ($user,$currency){
            $browser->loginAs($user)
                ->visit($this->url)
                ->assertSee('Add')
                ->value('input[name=price]',99.99)
                ->value('input[name=date_time_open]',now()->timestamp)
                ->value('input[name=date_time_close]',now()->timestamp+3600)
                ->value('input[name=currency_id]',$currency->id)
                ->press('Add')
                ->assertSee('Lot has been added successfully!');
        });
    }


    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function test_add_lot_negative_price()
    {
        $user = factory(User::class)->create();
        $currency = factory(Currency::class)->create();

        $this->browse(function(Browser $browser) use ($user,$currency){
            $browser->loginAs($user)
                ->visit($this->url)
                ->assertSee('Add')
                ->value('input[name=price]',-1)
                ->value('input[name=date_time_open]',now()->timestamp)
                ->value('input[name=date_time_close]',now()->timestamp+3600)
                ->value('input[name=currency_id]',$currency->id)
                ->press('Add')
                ->assertSee('Sorry, error has been occurred: Price must be positive');
        });
    }

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function test_add_lot_valid_close_before_open()
    {
        $user = factory(User::class)->create();
        $currency = factory(Currency::class)->create();

        $this->browse(function(Browser $browser) use ($user,$currency){
            $browser->loginAs($user)
                ->visit($this->url)
                ->assertSee('Add')
                ->value('input[name=price]',99.99)
                ->value('input[name=date_time_open]',now()->timestamp)
                ->value('input[name=date_time_close]',now()->timestamp-3600)
                ->value('input[name=currency_id]',$currency->id)
                ->press('Add')
                ->assertSee('Sorry, error has been occurred: Close datetime can\'t be before open');
        });
    }

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function test_add_lot_already_active()
    {
        $user = factory(User::class)->create();
        $currency = factory(Currency::class)->create();

        $this->browse(function(Browser $browser) use ($user,$currency){
            $browser->loginAs($user)
                ->visit($this->url)
                ->assertSee('Add')
                ->value('input[name=price]',99.99)
                ->value('input[name=date_time_open]',Carbon::now()->timestamp)
                ->value('input[name=date_time_close]',Carbon::now()->timestamp+3600)
                ->value('input[name=currency_id]',$currency->id)
                ->press('Add')
                ->assertSee('Lot has been added successfully!')
                ->value('input[name=price]',99.99)
                ->value('input[name=date_time_open]',Carbon::now()->timestamp)
                ->value('input[name=date_time_close]',Carbon::now()->timestamp+3600)
                ->value('input[name=currency_id]',$currency->id)
                ->press('Add')
                ->assertSee('Sorry, error has been occurred: User already has active currency lot');

        });
    }

}