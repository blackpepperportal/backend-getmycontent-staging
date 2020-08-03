<?php

use Illuminate\Database\Seeder;

use App\Helpers\Helper;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(Schema::hasTable('admins')) {

            $check_admin_details = DB::table('admins')->where('email' , 'admin@fansclub.com')->count();

            if(!$check_admin_details) {

            	DB::table('admins')->insert([
            		[
        		        'name' => 'Admin',
                        'unique_id' => 'admin-demo',
        		        'email' => 'admin@fansclub.com',
                        'about' => 'About',
        		        'password' => \Hash::make('123456'),
        		        'picture' => env('APP_URL')."/placeholder.jpeg",
                        'status' => 1,
                        'timezone' => 'Asia/Kolkata',
        		        'created_at' => date('Y-m-d H:i:s'),
        		        'updated_at' => date('Y-m-d H:i:s')
        		    ]
                ]);

            }

            $check_test_admin_details = DB::table('admins')->where('email' , 'test@fansclub.com')->count();

            if(!$check_test_admin_details) {

                DB::table('admins')->insert([

                    [
                        'name' => 'Test',
                        'unique_id' => 'admin-demo',
                        'email' => 'test@fansclub.com',
                        'password' => \Hash::make('123456'),
                        'about' => 'About',
                        'picture' => env('APP_URL')."/placeholder.jpeg",
                        'status' => 1,
                        'timezone' => 'Asia/Kolkata',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ],
    		    ]);
            }
        
        }

        if(Schema::hasTable('users')) {

            $check_admin_details = DB::table('users')->where('email' , 'user@fansclub.com')->count();

            if(!$check_admin_details) {

                DB::table('users')->insert([
                    [
                        'name' => 'user',
                        'email' => 'user@fansclub.com',
                        'password' => \Hash::make('123456'),
                        'picture' => env('APP_URL')."/placeholder.jpeg",
                        'login_by' => 'manual',
                        'mobile' => '9836367763',
                        'device_type' => 'web',
                        'status' => USER_APPROVED,
                        'is_verified' => USER_EMAIL_VERIFIED,
                        'token' => Helper::generate_token(),
                        'token_expiry' => Helper::generate_token_expiry(),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]
                ]);

            }

            $check_test_admin_details = DB::table('users')->where('email' , 'test@fansclub.com')->count();

            if(!$check_test_admin_details) {

                DB::table('users')->insert([
                    [
                        'name' => 'Test',
                        'email' => 'test@fansclub.com',
                        'password' => \Hash::make('123456'),
                        'picture' => env('APP_URL')."/placeholder.jpeg",
                        'login_by' => 'manual',
                        'mobile' => '9836367763',
                        'device_type' => 'web',
                        'status' => USER_APPROVED,
                        'is_verified' => USER_EMAIL_VERIFIED,
                        'token' => Helper::generate_token(),
                        'token_expiry' => Helper::generate_token_expiry(),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ],
                ]);
            }
        
        }

    }
}
