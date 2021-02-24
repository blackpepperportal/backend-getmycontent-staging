<?php

use Illuminate\Database\Seeder;

class CommissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('settings')->insert([
    		[
		        'key' => 'tips_admin_commission',
		        'value' => 10
		    ],
		    [
		        'key' => 'subscription_admin_commission',
		        'value' => 10
		    ]
		]);
    }
}
