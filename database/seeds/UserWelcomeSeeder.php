<?php

use Illuminate\Database\Seeder;

class UserWelcomeSeeder extends Seeder
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
		        'key' => 'is_welcome_steps',
		        'value' => 1
		    ]
		]);
    }
}
