<?php

use Illuminate\Database\Seeder;

class VerifiedBadgeSeeder extends Seeder
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
		        'key' => 'verified_badge_file',
		        'value' => asset('images/verified.svg')
		    ],
            [
                'key' => 'verified_badge_text',
                'value' => "Verified"
            ]
		]);
    }
}
