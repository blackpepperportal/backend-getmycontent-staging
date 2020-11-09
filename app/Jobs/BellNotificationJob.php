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
            
            $bell_notification = new BellNotification;

            $bell_notification->from_user_id = $datas['from_user_id'];

            $bell_notification->to_user_id = $datas['to_user_id'];

            $bell_notification->image = $datas['image'];

            $bell_notification->subject = $datas['subject'];

            $bell_notification->message = $datas['message'];

            $bell_notification->action_url = $datas['action_url'];

            $bell_notification->is_read = BELL_NOTIFICATION_STATUS_UNREAD;

            $bell_notification->save();
            
        } catch(Exception $e) {

            Log::info("BellNotificationJob - ERROR".print_r($e->getMessage(), true));
        }
        
    }
}
