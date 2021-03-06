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
		        'key' => 'is_watermark_logo_enabled',
		        'value' => NO
		    ],
    		[
		        'key' => 'watermark_logo',
		        'value' => env('APP_URL').'/watermark.png'
		    ]
		]);
    }
}
