<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Post;

use Carbon\Carbon;

use Log, Auth;

use Setting, Exception;

use App\Helpers\Helper;

use App\User;

class PostLikeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        //
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        try {

            $post_like = $this->data['post_like'];

            $title = $content = Helper::push_message(603);

            $message = tr('post_like_message', $post_like->User->name ?? ''); 

            $data['from_user_id'] = $post_like->user_id;

            $data['to_user_id'] = $post_like->post_user_id;
          
            $data['message'] = $message;

            $data['action_url'] = Setting::get('frontend_url')."/user/post_likes" ?? '';

            $data['image'] = $post_like->User->picture ?? asset('placeholder.jpeg');

            $data['subject'] = $content;

            dispatch(new BellNotificationJob($data));

            $user_details = User::where('id', $post_like->post_user_id)->first();

            if (Setting::get('is_push_notification') == YES && $user_details) {

                if($user_details->is_push_notification == YES && ($user_details->device_token != '')) {

                    $push_data = ['action_url'=>$data['action_url']];

                    \Notification::send($user_details->id, new \App\Notifications\PushNotification($title , $content, $push_data, $user_details->device_token));


                }
            }            

        } catch(Exception $e) {

            Log::info("Error ".print_r($e->getMessage(), true));

        }
    }
}
