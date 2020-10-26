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
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function home(Request $request) {

        try {

            $base_query = $total_query = Post::with('postFiles')->orderBy('created_at', 'asc');

            $posts = $base_query->skip($this->skip)->take($this->take)->get();

            foreach ($posts as $key => $post) {

                $post->is_user_needs_pay = $post->is_paid_post;

            }

            $data['posts'] = $posts ?? [];

            $data['total'] = $total_query->count() ?? 0;

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
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function posts_search(Request $request) {

        try {

            $base_query = $total_query = Post::with(['postFiles', 'user'])->orderBy('created_at', 'asc');

            if($request->search_key) {

                $base_query = $base_query->where('posts.content','LIKE','%'.$request->search_key.'%');

                $search_key = $request->search_key;

                $base_query = $base_query->whereHas('user', function($q) use($search_key) {
                                    $q->orWhere('name','LIKE','%'.$search_key.'%');
                                });
            }

            $posts = $base_query->skip($this->skip)->take($this->take)->get();

            foreach ($posts as $key => $post) {

                $post->is_user_needs_pay = $post->is_paid_post;

            }

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
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param integer $subscription_id
     *
     * @return JSON Response
     */
    public function posts_view_for_others(Request $request) {

        try {

            $rules = ['post_id' => 'required|exists:posts,id'];

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
     * @method posts_for_owner()
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
    public function posts_for_owner(Request $request) {

        try {

            $base_query = $total_query = Post::orderBy('created_at', 'asc');

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
     * @created Bhawya N
     *
     * @updated Bhawya N
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

            $post = Post::find($request->post_id);

            if(!$post) {
                throw new Exception(api_error(139), 139);   
            }

            $post->post_files = \App\PostFile::where('post_id', $request->post_id)->get();

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
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param integer $subscription_id
     *
     * @return JSON Response
     */
    public function posts_save_for_owner(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = [
                'content' => 'required|max:191',
                'publish_time' => 'nullable',
                'amount' => 'nullable|min:0',
                'files' => 'nullable'
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

                    foreach ($files as $key => $file) {

                        $file_input = ['post_id' => $post->id, 'file' => $file];

                        $post_file = \App\PostFile::create($file_input);
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
     * @created Bhawya N
     *
     * @updated Bhawya N
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

            $payment_response = PaymentRepo::posts_payment_save($request, $post)->getData();

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

                $payment_response = PaymentRepo::posts_payment_save($request, $post)->getData();

                if(!$payment_response->success) {

                    throw new Exception($payment_response->error, $payment_response->error_code);
                }

                // Update the to user

                $to_user_inputs = [
                    'id' => $post->user_id,
                    'received_from_user_id' => $request->id,
                    'total' => $post->amount, 
                    'user_pay_amount' => $post->amount,
                    'paid_amount' => $post->amount,
                    'payment_type' => WALLET_PAYMENT_TYPE_CREDIT,
                    'amount_type' => WALLET_AMOUNT_TYPE_ADD,
                    'payment_id' => 'CD-'.rand()
                ];

                $to_user_request = new \Illuminate\Http\Request();

                $to_user_request->replace($to_user_inputs);

                $to_user_payment_response = PaymentRepo::user_wallets_payment_save($to_user_request)->getData();

                if($to_user_payment_response->success) {

                    DB::commit();

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

            $base_query = $total_query = \App\PostComment::Approved()->where('posts.id',  $request->post_id)->orderBy('post_comments.created_at', 'desc');


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
                'post_id' => 'required|exists:posts,id',
            ];

            $custom_errors ['post_id.required' => api_error(146)];

            Helper::custom_validator($request->all(),$rules, $custom_errors);

            $request->request->add(['user_id' => $request->id]);

            $post_comment = \App\PostComment::updateOrCreate($request->all());

            DB::commit(); 

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

            $custom_errors ['post_comment_id.required' => api_error(151)];

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

            $base_query = $total_query = \App\PostBookmark::Approved()->orderBy('post_bookmarks.created_at', 'desc');

            $post_bookmarks = $base_query->skip($this->skip)->take($this->take)->get();

            $data['post_bookmarks'] = $post_bookmarks ?? [];

            $data['total'] = $total_query->count() ?? 0;

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

            $custom_errors ['post_id.required' => api_error(146)];

            Helper::custom_validator($request->all(),$rules, $custom_errors);

            $request->request->add(['user_id' => $request->id]);

            $post_bookmark = \App\PostBookmark::updateOrCreate($request->all());

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

            $custom_errors ['post_bookmark_id.required' => api_error(152)];

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
     * @method fav_posts()
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

    public function fav_posts(Request $request) {

        try {

           // Check the subscription is available

            $base_query = $total_query = \App\FavPost::Approved()->orderBy('fav_posts.created_at', 'desc');

            $fav_posts = $base_query->skip($this->skip)->take($this->take)->get();

            $data['fav_posts'] = $fav_posts ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = '' , $code = '', $data);
        
        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method fav_posts_save()
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
    public function fav_posts_save(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = ['post_id' => 'nullable|exists:posts,id'];

            $custom_errors ['post_id.required' => api_error(146)];

            Helper::custom_validator($request->all(),$rules, $custom_errors);

            $post = Post::find($request->post_id);

            if(!$post) {
                throw new Exception(api_error(139), 139);   
            }

            $request->request->add(['user_id' => $request->id, 'post_user_id' => $post->user_id]);

            $fav_post = \App\FavPost::updateOrCreate($request->all());

            DB::commit(); 

            $data = $fav_post;

            return $this->sendResponse(api_success(144), 144, $data);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method fav_posts_delete()
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
    public function fav_posts_delete(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = ['fav_post_id' => 'required|exists:fav_posts,id'];

            $custom_errors ['fav_post_id.required' => api_error(153)];

            Helper::custom_validator($request->all(),$rules, $custom_errors);

            $fav_post = \App\FavPost::destroy($request->fav_post_id);

            DB::commit(); 

            $data = $fav_post;

            return $this->sendResponse(api_success(145), 145, $data);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

}