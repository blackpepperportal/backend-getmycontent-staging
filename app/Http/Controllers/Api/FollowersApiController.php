<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper;

use App\Jobs\FollowUserJob;

use DB, Log, Hash, Validator, Exception, Setting;

use App\User, App\Follower;

class FollowersApiController extends Controller
{
    protected $loginUser;

    protected $skip, $take;

    public function __construct(Request $request) {

        Log::info(url()->current());

        Log::info("Request Data".print_r($request->all(), true));
        
        $this->loginUser = User::find($request->id);

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

        $this->timezone = $this->loginUser->timezone ?? "America/New_York";

    }

    /** 
     * @method user_suggestions()
     *
     * @uses Follow users & content creators
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param
     * 
     * @return JSON response
     *
     */
    public function user_suggestions(Request $request) {

        try {

            $following_user_ids = Follower::where('follower_id', $request->id)->pluck('user_id')->toArray();

            array_push($following_user_ids, $request->id);

            $base_query = $total_query = User::DocumentVerified()->Approved()->OtherResponse()->whereNotIn('users.id', $following_user_ids)->orderBy('users.created_at', 'desc');

            $users = $base_query->skip($this->skip)->take($this->take)->get();

            $data['users'] = $users;

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method follow_users()
     *
     * @uses Follow users & content creators
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param
     * 
     * @return JSON response
     *
     */
    public function follow_users(Request $request) {

        try {

            DB::beginTransaction();
            
            // Validation start
            // Follower id
            $rules = [
                'user_id' => 'required|exists:users,id'
            ];

            $custom_errors = ['user_id' => api_error(135)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);
            
            // Validation end
            if($request->id == $request->user_id) {

                throw new Exception(api_error(136), 136);

            }

            $follow_user = User::where('id', $request->user_id)->first();

            if(!$follow_user) {

                throw new Exception(api_error(135), 135);
            }


            // Check the user already following the selected users
            $follower = Follower::where('status', YES)->where('follower_id', $request->id)->where('user_id', $request->user_id)->first();

            if($follower) {

                throw new Exception(api_error(137), 137);

            }

            $follower = new Follower;

            $follower->user_id = $request->user_id;

            $follower->follower_id = $request->id;

            $follower->status = DEFAULT_TRUE;

            $follower->save();

            DB::commit();

            $job_data['follower'] = $follower;

            $job_data['timezone'] = $this->timezone;

            $this->dispatch(new FollowUserJob($job_data));

            $data['user_id'] = $request->user_id;

            $data['is_follow'] = NO;

            return $this->sendResponse(api_success(128,$follow_user->username ?? 'user'), $code = 128, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method unfollow_users()
     *
     * @uses Unfollow users/content creators
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param
     * 
     * @return JSON response
     *
     */
    public function unfollow_users(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = ['user_id' => 'required|exists:users,id'];

            $custom_errors = ['user_id' => api_error(135)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);
            
            // Validation end

            if($request->id == $request->user_id) {

                throw new Exception(api_error(136), 136);

            }

            // Check the user already following the selected users
            $follower = Follower::where('user_id', $request->user_id)->where('follower_id', $request->id)->where('status', YES)->delete();

            DB::commit();

            $data['user_id'] = $request->user_id;

            $data['is_follow'] = YES;

            return $this->sendResponse(api_success(129), $code = 129, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method followers()
     *
     * @uses Followers List
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param
     * 
     * @return JSON response
     *
     */
    public function followers(Request $request) {

        try {

            $base_query = $total_query = Follower::CommonResponse()->where('user_id', $request->id);

            $followers = $base_query->skip($this->skip)->take($this->take)->orderBy('followers.created_at', 'desc')->get();

            foreach ($followers as $key => $follower) {

                $follower->is_owner = $request->id == $follower->follower_id ? YES : NO;

                $is_you_following = Helper::is_you_following($request->id, $follower->user_id);

                $follower->show_follow = $is_you_following ? HIDE : SHOW;

                $follower->show_unfollow = $is_you_following ? SHOW : HIDE;

            }

            $data['followers'] = $followers;

            $data['total'] = $total_query->count() ?: 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method followings()
     *
     * @uses Followings list
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function followings(Request $request) {

        try {

            $base_query = $total_query = Follower::CommonResponse()->where('follower_id', $request->id);

            $followers = $base_query->skip($this->skip)->take($this->take)->orderBy('followers.created_at', 'desc')->get();

            foreach ($followers as $key => $follower) {

                $follower->is_owner = $request->id == $follower->follower_id ? YES : NO;

                $is_you_following = Helper::is_you_following($request->id, $follower->user_id);

                $follower->show_follow = $is_you_following ? HIDE : SHOW;

                $follower->show_unfollow = $is_you_following ? SHOW : HIDE;

            }

            $data['followers'] = $followers;

            $data['total'] = $total_query->count() ?: 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method chat_users()
     *
     * @uses chat_users List
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param
     * 
     * @return JSON response
     *
     */
    public function chat_users(Request $request) {

        try {

            $base_query = $total_query = \App\ChatUser::where('from_user_id', $request->id);

            $chat_users = $base_query->skip($this->skip)->take($this->take)
                    ->orderBy('chat_users.updated_at', 'desc')
                    ->get();

            foreach ($chat_users as $key => $chat_user) {

                $chat_user->message = ".....";

                $chat_user->time_formatted = common_date($chat_user->created_at, $this->timezone, 'd M Y');
            }

            $data['users'] = $chat_users ?? [];

            $data['total'] = $total_query->count() ?: 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method chat_messages()
     *
     * @uses chat_messages List
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param
     * 
     * @return JSON response
     *
     */
    public function chat_messages(Request $request) {

        try {

            $base_query = $total_query = \App\ChatMessage::where(function($query) use ($request){
                        $query->where('chat_messages.from_user_id', 'LIKE', '%'.$request->from_user_id.'%');
                        $query->where('chat_messages.to_user_id', 'LIKE', '%'.$request->to_user_id.'%');
                    })->orWhere(function($query) use ($request){
                        $query->where('chat_messages.from_user_id', 'LIKE', '%'.$request->to_user_id.'%');
                        $query->where('chat_messages.to_user_id', 'LIKE', '%'.$request->from_user_id.'%');
                    });

            $chat_messages = $base_query->skip($this->skip)->take($this->take)->orderBy('chat_messages.updated_at', 'desc')->get();

            $data['messages'] = $chat_messages ?? [];

            $data['user'] = $request->id == $request->from_user_id ? \App\User::find($request->to_user_id) : \App\User::find($request->to_user_id);

            $data['total'] = $total_query->count() ?: 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }


    /** 
     * @method followers()
     *
     * @uses Active Followers List
     *
     * @created Ganesh
     *
     * @updated Ganesh
     *
     * @param
     * 
     * @return JSON response
     *
     */
    public function active_followers(Request $request) {

        try {

            $base_query = $total_query = Follower::CommonResponse()->where('followers.status',FOLLOWER_ACTIVE)->where('user_id', $request->id);

            $followers = $base_query->skip($this->skip)->take($this->take)->orderBy('followers.created_at', 'desc')->get();

            foreach ($followers as $key => $follower) {

                $follower->is_owner = $request->id == $follower->follower_id ? YES : NO;

                $is_you_following = Helper::is_you_following($request->id, $follower->user_id);

                $follower->show_follow = $is_you_following ? HIDE : SHOW;

                $follower->show_unfollow = $is_you_following ? SHOW : HIDE;

            }

            $data['followers'] = $followers;

            $data['total'] = $total_query->count() ?: 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }


    /** 
     * @method followers()
     *
     * @uses Expired Followers List
     *
     * @created Ganesh
     *
     * @updated Ganesh
     *
     * @param
     * 
     * @return JSON response
     *
     */
    public function expired_followers(Request $request) {

        try {

            $base_query = $total_query = Follower::CommonResponse()->where('followers.status',FOLLOWER_EXPIRED)->where('user_id', $request->id);

            $followers = $base_query->skip($this->skip)->take($this->take)->orderBy('followers.created_at', 'desc')->get();

            foreach ($followers as $key => $follower) {

                $follower->is_owner = $request->id == $follower->follower_id ? YES : NO;

                $is_you_following = Helper::is_you_following($request->id, $follower->user_id);

                $follower->show_follow = $is_you_following ? HIDE : SHOW;

                $follower->show_unfollow = $is_you_following ? SHOW : HIDE;

            }

            $data['followers'] = $followers;

            $data['total'] = $total_query->count() ?: 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }
}