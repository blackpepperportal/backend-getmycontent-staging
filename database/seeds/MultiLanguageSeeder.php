<?php

use Illuminate\Database\Seeder;

class MultiLanguageSeeder extends Seeder
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
		        'key' => 'is_multilanguage_enabled',
		        'value' => NO
		    ]
		]);
    }
}
