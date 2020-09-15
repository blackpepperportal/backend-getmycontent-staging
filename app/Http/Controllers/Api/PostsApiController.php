<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper;

use DB, Log, Hash, Validator, Exception, Setting;

use App\Post, App\PostAlbum;

class PostsApiController extends Controller
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
     * @method posts_index()
     *
     * @uses To display all the posts
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function posts_index(Request $request) {

        try {

            $base_query = $total_query = Post::orderBy('created_at', 'asc');

            $posts = $base_query->skip($this->skip)->take($this->take)->get();

            $data['posts'] = $posts ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = '' , $code = '', $posts);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method posts_view()
     *
     * @uses get the selected post details
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param integer $subscription_id
     *
     * @return JSON Response
     */
    public function posts_view(Request $request) {

        try {

            $rules = [
                'post_id' => 'required|exists:post_id,id,user_id,'.$request->id
            ];

            Helper::custom_validator($request->all(),$rules);

            $post_details = Post::find($request->post_id);

            if(!$post_details) {
                throw new Exception(api_error(139), 139);   
            }

            $data['post_details'] = $post_details;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method posts_save()
     *
     * @uses get the selected post details
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param integer $subscription_id
     *
     * @return JSON Response
     */
    public function posts_save(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = [
                'content' => 'required|max:191',
                'publish_time' => 'required',
                'amount' => 'required',
                'is_paid_cost' => 'required',
            ];

            Helper::custom_validator($request->all(),$rules);

            $post_details = Post::find($request->post_id) ?? new Post;

            $success_code = $post_details->id ? 131 : 130;

            $post_details->user_id = $request->id;

            $post_details->content = $request->content ?: $post_details->content;

            $post_details->publish_time = $request->publish_time ?: $post_details->publish_time;

            $post_details->amount = $request->amount ?: '';

            $post_details->is_paid_cost = $request->is_paid_cost ?: $post_details->is_paid_cost;

            if($post_details->save()) {

                DB::commit(); 

                $data = Post::find($post_details->id);

                return $this->sendResponse(api_success($success_code), $success_code, $data);

            } 

            throw new Exception(api_error(128), 128);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

}