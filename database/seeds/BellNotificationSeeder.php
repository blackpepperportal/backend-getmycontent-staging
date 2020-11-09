<?php

use Illuminate\Database\Seeder;

class BellNotificationSeeder extends Seeder
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
		        'key' => 'BN_USER_FOLLOWINGS',
		        'value' => \Setting::get('frontend_url')."fans"
		    ],
		    [
		        'key' => 'BN_USER_COMMENT',
		        'value' => Setting::get('frontend_url')
		    ],
		    [
		        'key' => 'BN_USER_LIKE',
		        'value' => Setting::get('frontend_url')
		    ],
		    [
		        'key' => 'BN_USER_TIPS',
		        'value' => Setting::get('frontend_url')."payments"
		    ]
		]);
    }
}
