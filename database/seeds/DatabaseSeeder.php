<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DemoSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(PageDemoSeeder::class);
        $this->call(BellNotificationSeeder::class);
    }
}
