<?php

use Illuminate\Database\Seeder;

class NotificationCountSeeder extends Seeder
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
		        'key' => 'is_notification_count_enabled',
		        'value' => 0
		    ],
		    [
		        'key' => 'notification_time',
		        'value' => ''
		    ]
		]);
    }
}
