<?php

namespace App\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Helpers\Helper;

use Log; 

use App\Setting;

use App\User;

use App\BellNotification;

use App\BellNotificationTemplate;

use App\Jobs\Job;

use Exception;

class BellNotificationJob  implements ShouldQueue
{    
    use InteractsWithQueue, SerializesModels;

    protected $data;

    /**
    * The number of times the job may attempted.
    *
    * @var int 
    */
    public $tries =2;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {

            // Log::info('BellNotificationJob');

            $datas = $this->data;

            // Log::info($datas);
            
            $bell_notification_details = new BellNotification;

            $bell_notification_details->from_user_id = $datas['from_user_id'];

            $bell_notification_details->to_user_id = $datas['to_user_id'];

            $bell_notification_details->image = $datas['image'];

            $bell_notification_details->subject = $datas['subject'];

            $bell_notification_details->message = $datas['message'];

            $bell_notification_details->action_url = $datas['action_url'];

            $bell_notification_details->is_read = BELL_NOTIFICATION_STATUS_UNREAD;

            $bell_notification_details->save();
            
        } catch(Exception $e) {

            Log::info("BellNotificationJob - ERROR".print_r($e->getMessage(), true));
        }
        
    }
}
