<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper;

use DB, Log, Hash, Validator, Exception, Setting;

use App\User, App\Post;

use App\Repositories\PaymentRepository as PaymentRepo;

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
     * @method home()
     *
     * @uses To display all the posts
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function home(Request $request) {

        try {

            $follower_ids = get_follower_ids($request->id);

            $base_query = $total_query = Post::Approved()->whereIn('posts.user_id', $follower_ids)->orderBy('posts.created_at', 'desc');

            $posts = $base_query->skip($this->skip)->take($this->take)->get();

            $posts = \App\Repositories\PostRepository::posts_list_response($posts, $request);

            $data['posts'] = $posts ?? [];

            $data['total'] = $total_query->count() ?? 0;

            $data['user'] = $this->loginUser;

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method posts_search()
     *
     * @uses To display all the posts
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function posts_search(Request $request) {

        try {

            $follower_ids = get_follower_ids($request->id);

            $base_query = $total_query = Post::Approved()->whereIn('posts.user_id', $follower_ids)->with(['postFiles', 'user'])->orderBy('created_at', 'desc');

            if($request->search_key) {

                $base_query = $base_query->where('posts.content','LIKE','%'.$request->search_key.'%');

                $search_key = $request->search_key;

                $base_query = $base_query->whereHas('user', function($q) use($search_key) {
                                    $q->orWhere('name','LIKE','%'.$search_key.'%');
                                });
            }

            $posts = $base_query->skip($this->skip)->take($this->take)->get();

            $posts = \App\Repositories\PostRepository::posts_list_response($posts, $request);

            $data['posts'] = $posts ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method posts_view_for_others()
     *
     * @uses get the selected post details
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param integer $subscription_id
     *
     * @return JSON Response
     */
    public function posts_view_for_others(Request $request) {

        try {

            $rules = ['post_unique_id' => 'required|exists:posts,unique_id'];

            Helper::custom_validator($request->all(),$rules);

            $post = Post::with('postFiles')->Approved()->where('posts.unique_id', $request->post_unique_id)->first();

            if(!$post) {
                throw new Exception(api_error(139), 139);   
            }

            $post = \App\Repositories\PostRepository::posts_single_response($post, $request);

            $data['post'] = $post;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method posts_for_owner()
     *
     * @uses To display all the posts
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function posts_for_owner(Request $request) {

        try {

            $base_query = $total_query = Post::where('user_id', $request->id)->with('postFiles')->orderBy('posts.created_at', 'desc');

            $posts = $base_query->skip($this->skip)->take($this->take)->get();

            $data['posts'] = $posts ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method posts_view_for_owner()
     *
     * @uses get the selected post details
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param integer $subscription_id
     *
     * @return JSON Response
     */
    public function posts_view_for_owner(Request $request) {

        try {

            $rules = [
                'post_id' => 'required|exists:posts,id,user_id,'.$request->id
            ];

            Helper::custom_validator($request->all(),$rules);

            $post = Post::with('postFiles')->find($request->post_id);

            if(!$post) {
                throw new Exception(api_error(139), 139);   
            }

            $data['post'] = $post;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method posts_save_for_owner()
     *
     * @uses get the selected post details
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param integer $subscription_id
     *
     * @return JSON Response
     */
    public function posts_save_for_owner(Request $request) {

        try {
          
            DB::begintransaction();

            $rules = [
                'content' => 'required',
                'publish_time' => 'nullable',
                'amount' => 'nullable|min:0',
                'post_files' => 'nullable'
            ];

            Helper::custom_validator($request->all(),$rules);

            $post = Post::find($request->post_id) ?? new Post;

            $success_code = $post->id ? 131 : 130;

            $post->user_id = $request->id;

            $post->content = $request->content ?: $post->content;

            $publish_time = $request->publish_time ?: date('Y-m-d H:i:s');

            $post->publish_time = date('Y-m-d H:i:s', strtotime($publish_time));

            $amount = $request->amount ?: ($post->amount ?? 0);

            $post->amount = $amount;

            $post->is_paid_post = $amount > 0 ? YES : NO;

            if($post->save()) {

                if($request->post_files) {

                    $files = explode(',', $request->post_files);

                    foreach ($files as $key => $post_file_id) {

                        $file_input = ['post_id' => $post->id, 'file' => $file];

                        $post_file = \App\PostFile::find($post_file_id);

                        $post_file->post_id = $post->id;

                        // $old_path = get_post_temp_path($request->id, $file);

                        // $new_path = get_post_path($request->id, $file);

                        // $move = \Storage::move($old_path, $new_path);

                        // $file_path = POST_PATH.$request->id.'/'.basename($file);

                        // $post_file->file = \Storage::url($file_path);

                        // $post_file->file_type =  pathinfo($file,PATHINFO_EXTENSION);

                        // $post_file->blur_file = $post_file->file_type != "mp4" ? \App\Helpers\Helper::generate_post_blur_file($post_file->file, $request->id) : "";


                        $post_file->save();


                    }
                }

                DB::commit(); 

                $data = $post;

                return $this->sendResponse(api_success($success_code), $success_code, $data);

            } 

            throw new Exception(api_error(128), 128);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method post_files_upload()
     *
     * @uses get the selected post details
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param integer $subscription_id
     *
     * @return JSON Response
     */
    public function post_files_upload(Request $request) {

        try {
            
            $rules = [
                'file' => 'required|file|max:20000',
                'file_type' => 'required',
                'post_id' => 'nullable|posts,id'
            ];

            Helper::custom_validator($request->all(),$rules);

            $filename = rand(1,1000000).'-post-'.$request->file_type;

            $folder_path = POST_PATH.$request->id.'/';

            $post_file_url = Helper::post_upload_file($request->file, $folder_path, $filename);

            if($post_file_url) {

                $post_file = new \App\PostFile;

                $post_file->user_id = $request->id;

                $post_file->post_file = $post_file_url;

                $post_file->file_type = $request->file_type;

                $post_file->blur_file = $request->file_type == "image" ? \App\Helpers\Helper::generate_post_blur_file($post_file->file, $request->id) : Setting::get('post_video_placeholder');

                $post_file->save();

            }

            $data['file'] = $post_file_url;

            $data['post_file'] = $post_file;

            return $this->sendResponse(api_success(151), 151, $data);

            
        } catch(Exception $e){ 

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method post_files_remove()
     *
     * @uses remove the selected file
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param integer $post_file_id
     *
     * @return JSON Response
     */
    public function post_files_remove(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = [
                'file' => 'required',
                'post_file_id' => 'nullable|exists:post_files,id',
            ];

            Helper::custom_validator($request->all(),$rules);

            if($request->post_file_id) {

                \App\PostFile::where('id', $request->post_file_id)->delete();

            } else {

                \App\PostFile::where('file', $request->file)->delete();

                $folder_path = POST_TEMP_PATH.$request->id.'/';

                Helper::storage_delete_file($request->file, $folder_path);

            }

            DB::commit(); 

            return $this->sendResponse(api_success(152), 152, $data = []);
           
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method posts_delete_for_owner()
     *
     * @uses To delete content creators post
     *
     * @created Bhawya
     *
     * @updated  
     *
     * @param
     * 
     * @return response of details
     *
     */
    public function posts_delete_for_owner(Request $request) {

        try {

            DB::begintransaction();

            $rules = [
                'post_id' => 'required|exists:posts,id,user_id,'.$request->id
            ];

            Helper::custom_validator($request->all(),$rules,$custom_errors = []);

            $post = Post::find($request->post_id);

            if(!$post) {
                throw new Exception(api_error(139), 139);   
            }

            $post = \App\Post::destroy($request->post_id);

            DB::commit();

            $data['post_id'] = $request->post_id;

            return $this->sendResponse(api_success(134), $success_code = 134, $data);
            
        } catch(Exception $e){

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }       
         
    }

    /**
     * @method posts_status_for_owner
     *
     * @uses To update post status
     *
     * @created Bhawya
     *
     * @updated 
     *
     * @param object $request
     * 
     * @return response success/failure message
     *
     **/
    public function posts_status_for_owner(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'post_id' => 'required|exists:posts,id,user_id,'.$request->id
            ];

            Helper::custom_validator($request->all(),$rules,$custom_errors = []);

            $post = Post::find($request->post_id);

            if(!$post) {
                throw new Exception(api_error(139), 139);   
            }

            $post->is_published = $post->is_published ? UNPUBLISHED : PUBLISHED;

            if($post->save()) {

                DB::commit();

                $success_code = $post->is_published ? 135 : 136;

                $data['post'] = $post;

                return $this->sendResponse(api_success($success_code),$success_code, $data);

            }
            
            throw new Exception(api_error(130), 130);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /** 
     * @method posts_payment_by_stripe()
     *
     * @uses pay for subscription using paypal
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function posts_payment_by_stripe(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = ['post_id' => 'required|exists:posts,id'];

            $custom_errors = ['post_id' => api_error(139)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);
            
            // Validation end

           // Check the subscription is available

            $post = \App\Post::PaidApproved()->firstWhere('posts.id',  $request->post_id);

            if(!$post) {

                throw new Exception(api_error(146), 146);
                
            }

            $check_post_payment = \App\PostPayment::UserPaid($request->id, $request->post_id)->first();

            if($check_post_payment) {

                throw new Exception(api_error(145), 145);
                
            }

            $request->request->add(['payment_mode' => CARD]);

            $total = $user_pay_amount = $post->amount ?: 0.00;

            if($user_pay_amount > 0) {

                $user_card = \App\UserCard::where('user_id', $request->id)->firstWhere('is_default', YES);

                if(!$user_card) {

                    throw new Exception(api_error(120), 120); 

                }
                
                $request->request->add([
                    'total' => $total, 
                    'customer_id' => $user_card->customer_id,
                    'user_pay_amount' => $user_pay_amount,
                    'paid_amount' => $user_pay_amount,
                ]);

                $card_payment_response = PaymentRepo::posts_payment_by_stripe($request, $post)->getData();
                
                if($card_payment_response->success == false) {

                    throw new Exception($card_payment_response->error, $card_payment_response->error_code);
                    
                }

                $card_payment_data = $card_payment_response->data;

                $request->request->add(['paid_amount' => $card_payment_data->paid_amount, 'payment_id' => $card_payment_data->payment_id, 'paid_status' => $card_payment_data->paid_status]);

            }

            $payment_response = PaymentRepo::post_payments_save($request, $post)->getData();

            if($payment_response->success) {
                
                DB::commit();

                return $this->sendResponse(api_success(140), 140, $payment_response->data);

            } else {

                throw new Exception($payment_response->error, $payment_response->error_code);
                
            }
        
        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /**
     * @method posts_payment_by_wallet()
     * 
     * @uses send money to other user
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function posts_payment_by_wallet(Request $request) {

        try {
            
            DB::beginTransaction();

            // Validation start

            $rules = ['post_id' => 'required|exists:posts,id'];

            $custom_errors = ['post_id' => api_error(139)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);
            
            // Validation end

           // Check the subscription is available

            $post = \App\Post::PaidApproved()->firstWhere('posts.id',  $request->post_id);

            if(!$post) {

                throw new Exception(api_error(146), 146);
                
            }

            $check_post_payment = \App\PostPayment::UserPaid($request->id, $request->post_id)->first();

            if($check_post_payment) {

                throw new Exception(api_error(145), 145);
                
            }

            // Check the user has enough balance 

            $user_wallet = \App\UserWallet::where('user_id', $request->id)->first();

            $remaining = $user_wallet->remaining ?? 0;

            if($remaining < $post->amount) {
                throw new Exception(api_error(147), 147);    
            }
            
            $request->request->add([
                'payment_mode' => PAYMENT_MODE_WALLET,
                'total' => $post->amount, 
                'user_pay_amount' => $post->amount,
                'paid_amount' => $post->amount,
                'payment_type' => WALLET_PAYMENT_TYPE_PAID,
                'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                'payment_id' => 'WPP-'.rand()
            ]);

            $wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();

            if($wallet_payment_response->success) {

                $payment_response = PaymentRepo::post_payments_save($request, $post)->getData();

                if(!$payment_response->success) {

                    throw new Exception($payment_response->error, $payment_response->error_code);
                }

                return $this->sendResponse(api_success(140), 140, $payment_response->data ?? []);

            } else {

                throw new Exception($wallet_payment_response->error, $wallet_payment_response->error_code);
                
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method post_comments()
     * 
     * @uses list comments based on the post
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function post_comments(Request $request) {

        try {
            
            // Validation start

            $rules = ['post_id' => 'required|exists:posts,id'];

            $custom_errors = ['post_id' => api_error(139)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);
            
            // Validation end

            // Check the subscription is available

            $base_query = $total_query = \App\PostComment::Approved()->where('post_comments.post_id', $request->post_id)->orderBy('post_comments.created_at', 'desc');

            $post_comments = $base_query->skip($this->skip)->take($this->take)->get();

            $data['post_comments'] = $post_comments ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = '' , $code = '', $data);
        
        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method post_comments_save()
     *
     * @uses save the comments for the posts
     *
     * @created vithya
     *
     * @updated vithya
     *
     * @param object $request
     *
     * @return JSON Response
     */
    public function post_comments_save(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = [
                'comment' => 'required',
                'post_id' => 'required|exists:posts,id'
            ];

            $custom_errors = ['post_id.required' => api_error(146)];

            Helper::custom_validator($request->all(),$rules, $custom_errors);

            $custom_request = new Request();

            $custom_request->request->add(['user_id' => $request->id, 'post_id' => $request->post_id, 'comment' => $request->comment]);

            $post_comment = \App\PostComment::create($custom_request->request->all());

            DB::commit(); 

            $job_data['post_comment'] = $post_comment;

            $job_data['timezone'] = $this->timezone;

            $this->dispatch(new \App\Jobs\PostCommentJob($job_data));

            $data = $post_comment;

            return $this->sendResponse(api_success(141), 141, $data);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method post_comments_delete()
     *
     * @uses save the comments for the posts
     *
     * @created vithya
     *
     * @updated vithya
     *
     * @param object $request
     *
     * @return JSON Response
     */
    public function post_comments_delete(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = ['post_comment_id' => 'required|exists:post_comments,id'];

            $custom_errors = ['post_comment_id.required' => api_error(151)];

            Helper::custom_validator($request->all(),$rules, $custom_errors);

            $post_comment = \App\PostComment::destroy($request->post_comment_id);

            DB::commit(); 

            $data = $post_comment;

            return $this->sendResponse(api_success(142), 142, $data);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method post_bookmarks()
     * 
     * @uses list of bookmarks
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function post_bookmarks(Request $request) {

        try {

           // Check the subscription is available

            $base_query = \App\PostBookmark::where('user_id', $request->id)->Approved()->orderBy('post_bookmarks.created_at', 'desc');

            $post_ids = $base_query->skip($this->skip)->take($this->take)->pluck('post_id');

            $post_ids = $post_ids ? $post_ids->toArray() : [];

            if($post_ids) {

                $post_base_query = $total_query = \App\Post::with('postFiles')->Approved()->whereIn('posts.id', $post_ids)->orderBy('posts.created_at', 'desc');

                if($request->type != POSTS_ALL) {

                    $type = $request->type;

                    $post_base_query = $post_base_query->whereHas('postFiles', function($q) use($type) {
                            $q->where('post_files.file_type', $type);
                        });
                }

                $posts = $post_base_query->get();

                $posts = \App\Repositories\PostRepository::posts_list_response($posts, $request);

                $total = $total_query->count() ?? 0;

            }

            $data['posts'] = $posts ?? [];

            $data['total'] = $total ?? 0;

            return $this->sendResponse($message = '' , $code = '', $data);
        
        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method post_bookmarks_save()
     *
     * @uses save the comments for the posts
     *
     * @created vithya
     *
     * @updated vithya
     *
     * @param object $request
     *
     * @return JSON Response
     */
    public function post_bookmarks_save(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = ['post_id' => 'nullable|exists:posts,id'];

            $custom_errors = ['post_id.required' => api_error(146)];

            Helper::custom_validator($request->all(),$rules, $custom_errors);

            $custom_request = new Request();

            $custom_request->request->add(['user_id' => $request->id, 'post_id' => $request->post_id]);

            $post_bookmark = \App\PostBookmark::updateOrCreate($custom_request->request->all());

            DB::commit(); 

            $data = $post_bookmark;

            return $this->sendResponse(api_success(143), 143, $data);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method post_bookmarks_delete()
     *
     * @uses delete the bookmarks
     *
     * @created vithya
     *
     * @updated vithya
     *
     * @param object $request
     *
     * @return JSON Response
     */
    public function post_bookmarks_delete(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = ['post_bookmark_id' => 'required|exists:post_bookmarks,id'];

            $custom_errors = ['post_bookmark_id.required' => api_error(152)];

            Helper::custom_validator($request->all(),$rules, $custom_errors);

            $post_bookmark = \App\PostBookmark::destroy($request->post_bookmark_id);

            DB::commit(); 

            $data = $post_bookmark;

            return $this->sendResponse(api_success(142), 142, $data);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method post_likes()
     * 
     * @uses list of post likes
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function post_likes(Request $request) {

        try {

           // Check the subscription is available

            $base_query = $total_query = \App\PostLike::where('user_id', $request->id)->Approved()->orderBy('post_likes.created_at', 'desc');

            $post_likes = $base_query->skip($this->skip)->take($this->take)->get();

            $data['post_likes'] = $post_likes ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = '' , $code = '', $data);
        
        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method post_likes_save()
     *
     * @uses Add posts to fav list
     *
     * @created vithya
     *
     * @updated vithya
     *
     * @param object $request
     *
     * @return JSON Response
     */
    public function post_likes_save(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = ['post_id' => 'required|exists:posts,id'];
             
            $custom_errors = ['post_id.required' => api_error(139)];

            Helper::custom_validator($request->all(),$rules, $custom_errors);

            $post = \App\Post::Approved()->find($request->post_id);

            if(!$post) {

                throw new Exception(api_error(139), 139);   
            }

            $post_like = \App\PostLike::where('user_id', $request->id)->where('post_id', $request->post_id)->first();

            $code = 149;

            if(!$post_like) {

                $custom_request = new Request();

                $custom_request->request->add(['user_id' => $request->id, 'post_id' => $request->post_id, 'post_user_id' => $post->user_id]);

                $post_like = \App\PostLike::create($custom_request->request->all());

            } else{

                $post_like->delete();

                $code = 150;
            }

            DB::commit(); 


            $job_data['post_like'] = $post_like;

            $job_data['timezone'] = $this->timezone;

            $this->dispatch(new \App\Jobs\PostLikeJob($job_data));

            $data = $post_like;

            return $this->sendResponse(api_success($code), $code, $data);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method post_likes_delete()
     *
     * @uses delete the fav posts
     *
     * @created vithya
     *
     * @updated vithya
     *
     * @param object $request
     *
     * @return JSON Response
     */
    public function post_likes_delete(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = ['post_like_id' => 'required|exists:post_likes,id'];

            $custom_errors = ['post_like_id.required' => api_error(153)];

            Helper::custom_validator($request->all(),$rules, $custom_errors);

            $post_like = \App\FavUser::destroy($request->post_like_id);

            DB::commit(); 

            $data = $post_like;

            return $this->sendResponse(api_success(145), 145, $data);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method fav_users()
     * 
     * @uses list of fav posts
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function fav_users(Request $request) {

        try {

           // Check the subscription is available

            $base_query = $total_query = \App\FavUser::where('user_id', $request->id)->Approved()->orderBy('fav_users.created_at', 'desc');

            $fav_users = $base_query->skip($this->skip)->take($this->take)->get();

            $data['fav_users'] = $fav_users ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = '' , $code = '', $data);
        
        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method fav_users_save()
     *
     * @uses Add posts to fav list
     *
     * @created vithya
     *
     * @updated vithya
     *
     * @param object $request
     *
     * @return JSON Response
     */
    public function fav_users_save(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = ['user_id' => 'required|exists:users,id'];

            $custom_errors = ['user_id.required' => api_error(146)];

            Helper::custom_validator($request->all(),$rules, $custom_errors);

            $to_user = \App\User::Approved()->find($request->user_id);

            if(!$to_user) {
                throw new Exception(api_error(135), 135);
            }

            $check_fav_user = $fav_user = \App\FavUser::where('user_id', $request->id)->where('fav_user_id', $request->user_id)->first();

            if(!$check_fav_user) {

                $custom_request = new Request();

                $custom_request->request->add(['user_id' => $request->id, 'fav_user_id' => $request->user_id]);

                $fav_user = \App\FavUser::create($custom_request->request->all());

                $code = 144;

            } else {

                $check_fav_user->delete();

                $code = 145;

            }

            DB::commit(); 

            $data = $fav_user;

            return $this->sendResponse(api_success($code), $code, $data);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method fav_users_delete()
     *
     * @uses delete the fav posts
     *
     * @created vithya
     *
     * @updated vithya
     *
     * @param object $request
     *
     * @return JSON Response
     */
    public function fav_users_delete(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = ['fav_user_id' => 'required|exists:fav_users,id'];

            $custom_errors = ['fav_user_id.required' => api_error(153)];

            Helper::custom_validator($request->all(),$rules, $custom_errors);

            $fav_user = \App\FavUser::destroy($request->fav_user_id);

            DB::commit(); 

            $data = $fav_user;

            return $this->sendResponse(api_success(145), 145, $data);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /** 
     * @method tips_payment_by_stripe()
     *
     * @uses send tips to the user
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function tips_payment_by_stripe(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = [
                    'post_id' => 'nullable|exists:posts,id',
                    'user_id' => 'required|exists:users,id',
                    'amount' => 'required|min:0'
                ];

            $custom_errors = ['post_id' => api_error(139), 'user_id' => api_error(135)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);
            
            // Validation end

            if($request->id == $request->user_id) {
                throw new Exception(api_error(154), 154);
                
            }

            $post = \App\Post::PaidApproved()->firstWhere('posts.id',  $request->post_id);

            $user = \App\User::Approved()->firstWhere('users.id',  $request->user_id);

            if(!$user) {

                throw new Exception(api_error(135), 135);
                
            }

            $request->request->add(['payment_mode' => CARD, 'from_user_id' => $request->id, 'to_user_id' => $request->user_id]);

            $total = $user_pay_amount = $request->amount ?: 1;

            if($user_pay_amount > 0) {

                $user_card = \App\UserCard::where('user_id', $request->id)->firstWhere('is_default', YES);

                if(!$user_card) {

                    throw new Exception(api_error(120), 120); 

                }
                
                $request->request->add([
                    'total' => $total, 
                    'customer_id' => $user_card->customer_id,
                    'user_card_id' => $user_card->id,
                    'user_pay_amount' => $user_pay_amount,
                    'paid_amount' => $user_pay_amount,
                ]);

                $card_payment_response = PaymentRepo::tips_payment_by_stripe($request, $post)->getData();
                
                if($card_payment_response->success == false) {

                    throw new Exception($card_payment_response->error, $card_payment_response->error_code);
                }

                $card_payment_data = $card_payment_response->data;

                $request->request->add(['paid_amount' => $card_payment_data->paid_amount, 'payment_id' => $card_payment_data->payment_id, 'paid_status' => $card_payment_data->paid_status]);

            }

            $payment_response = PaymentRepo::tips_payment_save($request)->getData();

            if($payment_response->success) {
                
                DB::commit();
                
                
                $job_data['user_tips'] = $request->all();

                $job_data['timezone'] = $this->timezone;
    
                $this->dispatch(new \App\Jobs\TipPaymentJob($job_data));

                return $this->sendResponse(api_success(146), 146, $payment_response->data);

            } else {
              
                
                throw new Exception($payment_response->error, $payment_response->error_code);
                
            }
        
        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /**
     * @method tips_payment_by_wallet()
     * 
     * @uses send tips to the user
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function tips_payment_by_wallet(Request $request) {

        try {
            
            DB::beginTransaction();

            // Validation start

            $rules = [
                    'post_id' => 'nullable|exists:posts,id',
                    'user_id' => 'required|exists:users,id',
                    'amount' => 'required|min:0'
                ];

            $custom_errors = ['post_id' => api_error(139), 'user_id' => api_error(135)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);
            
            // Validation end

            if($request->id == $request->user_id) {
                throw new Exception(api_error(154), 154);
                
            }

            $user = \App\User::Approved()->firstWhere('users.id',  $request->user_id);

            if(!$user) {

                throw new Exception(api_error(135), 135);
                
            }

            // Check the user has enough balance 

            $user_wallet = \App\UserWallet::where('user_id', $request->id)->first();

            $remaining = $user_wallet->remaining ?? 0;

            if($remaining < $request->amount) {
                throw new Exception(api_error(147), 147);    
            }
            
            $request->request->add([
                'payment_mode' => PAYMENT_MODE_WALLET,
                'total' => $request->amount, 
                'user_pay_amount' => $request->amount,
                'paid_amount' => $request->amount,
                'payment_type' => WALLET_PAYMENT_TYPE_PAID,
                'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                'payment_id' => 'WPP-'.rand(),
                ''
            ]);

            $wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();

            if($wallet_payment_response->success) {

                $request->request->add(['to_user_id' => $request->user_id]);

                $payment_response = PaymentRepo::tips_payment_save($request)->getData();

                if(!$payment_response->success) {

                    throw new Exception($payment_response->error, $payment_response->error_code);
                }

                // Update the to user

                $to_user_inputs = [
                    'id' => $request->user_id,
                    'received_from_user_id' => $request->id,
                    'total' => $request->amount, 
                    'user_pay_amount' => $request->amount,
                    'paid_amount' => $request->amount,
                    'payment_type' => WALLET_PAYMENT_TYPE_CREDIT,
                    'amount_type' => WALLET_AMOUNT_TYPE_ADD,
                    'payment_id' => 'CD-'.rand()
                ];

                $to_user_request = new \Illuminate\Http\Request();

                $to_user_request->replace($to_user_inputs);

                $to_user_payment_response = PaymentRepo::user_wallets_payment_save($to_user_request)->getData();

                if($to_user_payment_response->success) {

                    DB::commit();

                    $user_tips = new \Illuminate\Http\Request();

                    $user_tips->amount = $request->amount;

                    $user_tips->user_id = $request->user_id;

                    $user_tips->id = $request->id;

                    $job_data['user_tips'] = $user_tips;

                    $job_data['timezone'] = $this->timezone;
        
                    $this->dispatch(new \App\Jobs\TipPaymentJob($job_data));

                    return $this->sendResponse(api_success(140), 140, $payment_response->data ?? []);

                } else {

                    throw new Exception($to_user_payment_response->error, $to_user_payment_response->error_code);
                }

            } else {

                throw new Exception($wallet_payment_response->error, $wallet_payment_response->error_code);
                
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }


}