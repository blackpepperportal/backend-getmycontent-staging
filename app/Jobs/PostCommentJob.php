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

class PostCommentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */

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

            $post_comment = $this->data['post_comment'];

            $title = $content = push_messages(604);

            $message = tr('post_comment_message', $post_comment->user->name ?? ''); 

            $data['from_user_id'] = $post_comment->user_id;

            $data['to_user_id'] = $post_comment->post->user_id;
          
            $data['message'] = $message;

            $data['action_url'] = Setting::get('BN_USER_COMMENT').$post_comment->post->post_unique_id;

            $data['image'] = $post_comment->user->picture ?? asset('placeholder.jpeg');

            $data['subject'] = $content;

            dispatch(new BellNotificationJob($data));

            $user = User::where('id', $post_comment->post->user_id)->first();

            if (Setting::get('is_push_notification') == YES && $user) {

                if($user->is_push_notification == YES && ($user->device_token != '')) {

                    $push_data = ['action_url'=>$data['action_url']];

                    \Notification::send($user->id, new \App\Notifications\PushNotification($title , $content, $push_data, $user->device_token));


                }
            }        
            
            
            if (Setting::get('is_email_notification') == YES && $user) {
               
                $email_data['subject'] = tr('user_post_comment_message');
               
                $email_data['message'] = $message;

                $email_data['page'] = "emails.posts.post_comment";

                $email_data['email'] = $user->email;

                $email_data['name'] = $user->name;

                $email_data['data'] = $user;

                dispatch(new SendEmailJob($email_data));


            }

        } catch(Exception $e) {

            Log::info("Error ".print_r($e->getMessage(), true));

        }
    }
    
}
