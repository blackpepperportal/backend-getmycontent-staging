<?php

use Illuminate\Database\Seeder;

class PaypalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
    		[
		        'key' => 'is_paypal_enabled',
		        'value' => 0
		    ],
		    [
		        'key' => 'PAYPAL_ID',
		        'value' => 'AaXkweZD5g9s0X3BsO0Y4Q-kNzbmLZaog0mbmVGrTT5IX0O73LoLVcHp17e6pkG7Vm04JEUuG6up30LD'
		    ],
		    [
		        'key' => 'PAYPAL_SECRET',
		        'value' => ''
		    ],
		    [
		        'key' => 'PAYPAL_MODE',
		        'value' => 'sandbox'
		    ]
		]);
    }
}
