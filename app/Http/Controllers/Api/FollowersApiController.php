<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper;

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

            $follow_user_details = User::Approved()->whereFirst('id', $request->user_id);

            if(!$follow_user_details) {

                throw new Exception(api_error(135), 135);
            }

            // Check the user already following the selected users
            $follow_user_details = Follower::where('status', YES)->where('follower', $request->id)->whereFirst('user_id', $request->user_id);

            if($follower_details) {

                throw new Exception(api_error(137), 137);

            }

            // Viewer or content creator -> Both can follow only creators.
            if($this->loginUser->is_content_creator == NO) {

                throw new Exception(api_error(138), 138);
            }

            && $follow_user_details->is_content_creator == NO

            $follower = new Follower;

            $follower->user_id = $request->user_id;

            $follower->follower = $request->id;

            $follower->status = DEFAULT_TRUE;

            $follower->save();

            DB::commit();

            $data['user_id'] = $request->user_id;

            $data['is_follow'] = NO;

            return $this->sendResponse(api_success(128,$follow_user_details->name ?? 'user'), $code = 128, $data);

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
            $follower_details = Follower::where('user_id', $request->user_id)->where('follower', $request->id)->where('status', YES)->delete();

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

            $followers = Follower::CommonResponse()
                    ->where('user_id', $request->id)
                    ->skip($this->skip)
                    ->take($this->take)
                    ->orderBy('followers.created_at', 'desc')
                    ->get();

            foreach ($followers as $key => $follower_details) {

                $follower_details->is_owner = $request->id == $follower_details->follower ? YES : NO;

                $is_you_following = Helper::is_you_following($request->id, $follower_details->user_id);

                $follower_details->show_follow = $is_you_following ? HIDE : SHOW;

                $follower_details->show_unfollow = $is_you_following ? SHOW : HIDE;

            }

            $data['followers'] => $followers;

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

            $followers = Follower::FollowingResponse()
                    ->where('follower', $request->id)
                    ->skip($this->skip)
                    ->take($this->take)
                    ->orderBy('followers.created_at', 'desc')
                    ->get();

            foreach ($followers as $key => $user_details) {

                $follower_details->is_owner = $request->id == $follower_details->follower ? YES : NO;

                $is_you_following = Helper::is_you_following($request->id, $follower_details->user_id);

                $follower_details->show_follow = $is_you_following ? HIDE : SHOW;

                $follower_details->show_unfollow = $is_you_following ? SHOW : HIDE;

            }

            $data['followers'] => $followers;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }
}