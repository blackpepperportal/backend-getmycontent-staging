<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Post, App\User;

use Carbon\Carbon;

use Log, Auth, Setting, Exception;

use App\Helpers\Helper;

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
        try {

            $post_like = $this->data['post_like'];

            $title = $content = push_messages(603);

            $message = tr('user_post_like_message', $post_like->User->name ?? ''); 

            $data['from_user_id'] = $post_like->user_id;

            $data['to_user_id'] = $post_like->post_user_id;

            $data['post_id'] = $post_like->post_id;
          
            $data['message'] = $message;

            $data['action_url'] = Setting::get('BN_USER_LIKE').$post_like->post->post_unique_id;

            $data['image'] = $post_like->User->picture ?? asset('placeholder.jpeg');

            $data['subject'] = $content;

            $data['type'] = BELL_NOTIFICATION_TYPE_LIKE;

            dispatch(new BellNotificationJob($data));

            $user = User::where('id', $post_like->post_user_id)->first();

            if (Setting::get('is_push_notification') == YES && $user) {

                if($user->is_push_notification == YES && ($user->device_token != '')) {

                    $push_data = ['action_url'=>$data['action_url']];

                    \Notification::send($user->id, new \App\Notifications\PushNotification($title , $content, $push_data, $user->device_token));

                }
            }      


            if (Setting::get('is_email_notification') == YES && $user) {

                $email_data['subject'] = tr('user_post_like_message', $post_like->User->name ?? ''); 
               
                $email_data['message'] = $message;

                $email_data['page'] = "emails.posts.post_like";

                $email_data['email'] = $user->email;

                $email_data['name'] = $user->name;

                $email_data['data'] = $user;

                Log::info("message_save".print_r($email_data['email'], true));

                dispatch(new SendEmailJob($email_data));


            }

        } catch(Exception $e) {

            Log::info("Error ".print_r($e->getMessage(), true));

        }
    }
}
