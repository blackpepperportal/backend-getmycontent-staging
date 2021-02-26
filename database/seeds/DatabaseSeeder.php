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
        $this->call(SettingSeeder::class);
        $this->call(DemoSeeder::class);

        $this->call(BellNotificationSeeder::class);
        $this->call(CommissionSeeder::class);
        $this->call(NotificationCountSeeder::class);
        $this->call(PageDemoSeeder::class);
        $this->call(PaypalSeeder::class);
        $this->call(VerifiedBadgeSeeder::class);

    }
}
