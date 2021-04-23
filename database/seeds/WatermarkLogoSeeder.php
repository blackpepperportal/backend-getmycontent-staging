<?php

use Illuminate\Database\Seeder;

class WatermarkLogoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        \DB::table('settings')->insert([
    		[
		        'key' => 'watermark_logo',
		        'value' => 1
		    ]
		]);
    }
}
