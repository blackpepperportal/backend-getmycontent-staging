<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;

use Exception;

use App\Mail\SendEmail;

use DB, Hash, Setting, Auth, Validator, Enveditor,Log;

use Mailgun\Mailgun;



class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

   protected $email_data;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 2;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email_data)
    {
        $this->email_data = $email_data; 


    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            
            $mail_model = new SendEmail($this->email_data);

            $isValid = 1;

            Log::info("mailer - ".Setting::get('MAILGUN_DOMAIN'));


            if(envfile('MAIL_MAILER') == 'mailgun' && Setting::get('MAILGUN_PUBLIC_KEY')!='') {


                Log::info("isValid - START");

                # Instantiate the client.

                $email_address = Mailgun::create(Setting::get('MAILGUN_SECRET'));

                $validateAddress = $this->email_data['email'];


                # Issue the call to the client.

                $result =  $email_address->domains()->verify($validateAddress);
                // // # is_valid is 0 or 1

                $isValid = $result->http_response_body->is_valid;

                Log::info("isValid FINAL STATUS - ".$isValid);

            }

            if($isValid) {

                \Mail::queue($mail_model);

                Log::info("EmailJob Success");
            }

        } catch(Exception $e) {

            Log::info("SendEmailJob Error".print_r($e->getMessage(), true));

        }

    }
}
