<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper, App\Helpers\EnvEditorHelper;

use DB, Hash, Setting, Auth, Validator, Exception, Enveditor;

use App\Jobs\SendEmailJob;

use App\Jobs\PublishPostJob;

use Carbon\Carbon;

class AdminPostController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request) {

        $this->middleware('auth:admin');

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

    }

    /**
     * @method posts_index()
     *
     * @uses Display the total posts
     *
     * @created Akshata
     *
     * @updated
     *
     * @param -
     *
     * @return view page 
     */
    public function posts_index(Request $request) {

        $base_query = \App\Post::orderBy('created_at','DESC');

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query =  $base_query

            ->whereHas('user', function($q) use ($search_key) {

                return $q->Where('users.name','LIKE','%'.$search_key.'%');

            })->orWhere('posts.amount','LIKE','%'.$search_key.'%');

        }

        if($request->status) {

            switch ($request->status) {

                case SORT_BY_APPROVED:
                    $base_query = $base_query->where('posts.status', APPROVED);
                    break;

                case SORT_BY_DECLINED:
                    $base_query = $base_query->where('posts.status', DECLINED);
                    break;

                case SORT_BY_FREE_POST:
                    $base_query = $base_query->where('posts.is_paid_post',FREE_POST);
                    break;

                default:
                    $base_query = $base_query->where('posts.is_paid_post',PAID_POST);
                    break;
            }
        }

        if($request->scheduled) {

            $base_query = $base_query->where('is_published',NO);

            $posts = $base_query->paginate(10);

            return view('admin.posts.index')
                        ->with('page','scheduled-posts')
                        ->with('posts', $posts);
        }

        if($request->user_id) {

            $base_query = $base_query->where('user_id',$request->user_id);
        }

        $posts = $base_query->paginate(10);

        return view('admin.posts.index')
                ->with('page', 'posts')
                ->with('sub_page', 'posts-view')
                ->with('posts', $posts);
    
    }

    /**
     * @method posts_create()
     *
     * @uses create new post
     *
     * @created sakthi 
     *
     * @updated 
     *
     * 
     * @return View page
     *
    */
    public function posts_create() {

        $post = new \App\Post;

        $users = \App\User::Approved()->get();

        return view('admin.posts.create')
                ->with('page', 'posts')
                ->with('sub_page', 'posts-create')
                ->with('users', $users)
                ->with('post', $post);  

    }

    /**
     * @method posts_save()
     *
     * @uses save new post
     *
     * @created sakthi 
     *
     * @updated 
     *
     * 
     * @return View page
     *
    */
    public function posts_save(Request $request) {
        
        try {
            
            
            DB::begintransaction();

            $rules = [
                'user_id' => 'required',
                'content' => 'required',
                'amount' => 'nullable|min:0',
                'publish_type'=>'required',
                'publish_time' => 'required_if:publish_type,==,'.UNPUBLISHED,
            ];

            $customMessages = [
                'required_if' => 'The :attribute field is required when :other is '. tr('schedule') .'.',
            ];


            Helper::custom_validator($request->all(),$rules, $customMessages);

            $post = \App\Post::find($request->post_id) ?? new \App\Post;

            $post->user_id = $request->user_id;

            $post->content = $request->content;

            $post->is_published = $request->publish_type;

            $publish_time = $request->publish_time ?: date('Y-m-d H:i:s');
          
            $post->publish_time = date('Y-m-d H:i:s', strtotime($publish_time));

            $post->amount = $request->amount?? 0;

            $post->is_paid_post = $request->amount > 0 ? YES : NO;

            if($post->save()) {

                if($request->has('post_files')){

                    $post_file = \App\PostFile::where('post_id',$post->id)->first() ?? new \App\PostFile();

                    $filename = rand(1,1000000).'-post-'.$request->file_type ?? 'image';

                    $folder_path = POST_PATH.$post->user_id.'/';

                    $post_file_url = Helper::post_upload_file($request->post_files, $folder_path, $filename);

                    if($post_file_url) {

                        $post_file->post_id = $post->id;

                        $post_file->file = $post_file_url;

                        $post_file->file_type = 'image';

                        $post_file->blur_file = \App\Helpers\Helper::generate_post_blur_file($post_file->file, $post->user_id);
                        
                        $post_file->save();

                    }

                }
                DB::commit(); 

                return redirect()->route('admin.posts.view',['post_id'=>$post->id])->with('flash_success', tr('posts_create_succes'));

            } 

            throw new Exception(tr('post_save_failed'));

        } catch(Exception $e){ 

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error', $e->getMessage());

        } 

    }

    /**
     * @method posts_edit()
     *
     * @uses To display and update user details based on the user id
     *
     * @created sakthi
     *
     * @updated 
     *
     * @param object $request - User Id
     * 
     * @return redirect view page 
     *
    */
    public function posts_edit(Request $request) {

        try {

            $post = \App\Post::find($request->post_id);

            if(!$post) { 

                throw new Exception(tr('post_not_found'), 101);
            }
            
            $users = \App\User::Approved()->get();

            return view('admin.posts.edit')
                        ->with('page', 'post')
                        ->with('sub_page', 'posts-view')
                        ->with('users', $users)
                        ->with('post', $post); 

        } catch(Exception $e) {

            return redirect()->route('admin.posts.index')->with('flash_error', $e->getMessage());
        }

    }

    /**
     * @method posts_view()
     *
     * @uses displays the specified posts details based on post id
     *
     * @created Akshata 
     *
     * @updated 
     *
     * @param object $request - post Id
     * 
     * @return View page
     *
     */
    public function posts_view(Request $request) {

        try {

            $post = \App\Post::find($request->post_id);
            
            if(!$post) { 

                throw new Exception(tr('post_not_found'), 101);                
            }

            $payment_data = new \stdClass;

            $payment_data->total_earnings = \App\PostPayment::where('post_id',$request->post_id)->sum('paid_amount');

            $payment_data->current_month_earnings = \App\PostPayment::where('post_id',$request->post_id)->whereMonth('paid_date',date('m'))->sum('paid_amount');

            $payment_data->today_earnings = \App\PostPayment::where('post_id',$request->payment_id)->whereDate('paid_date',today())->sum('paid_amount');

            $post_files = \App\PostFile::where('post_id',$request->post_id)->get() ?? [];

            return view('admin.posts.view')
                    ->with('page', 'posts') 
                    ->with('sub_page','posts-view') 
                    ->with('post', $post)
                    ->with('post_files', $post_files)
                    ->with('payment_data',$payment_data);

        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }

    }

    /**
     * @method posts_delete()
     *
     * @uses delete the post details based on post id
     *
     * @created Akshata 
     *
     * @updated  
     *
     * @param object $request - Post Id
     * 
     * @return response of success/failure details with view page
     *
     */
    public function posts_delete(Request $request) {

        try {

            DB::begintransaction();

            $post = \App\Post::find($request->post_id);

            if(!$post) {

                throw new Exception(tr('post_not_found'), 101);                
            }

            if($post->delete()) {

                DB::commit();

                return redirect()->route('admin.posts.index',['page'=>$request->page])->with('flash_success', tr('post_deleted_success'));   

            } 

            throw new Exception(tr('post_delete_failed'));

        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       

    }



    /**
     * @method posts_dashboard()
     *
     * @uses displays the specified posts dashboard based on post id
     *
     * @created Sakthi 
     *
     * @updated 
     *
     * @param object $request - post Id
     * 
     * @return View page
     *
     */
    public function posts_dashboard(Request $request) {

        try {

            $post = \App\Post::find($request->post_id);

            if(!$post) { 

                throw new Exception(tr('post_not_found'), 101);                
            }

            $payment_data = new \stdClass;

            $data = new \stdClass;

            $payment_data->total_earnings = \App\PostPayment::where('post_id',$request->post_id)->sum('paid_amount');

            $payment_data->today_earnings = \App\PostPayment::where('post_id',$request->post_id)->whereDate('paid_date',today())->sum('paid_amount');

            $number_of_likes = \App\PostLike::Approved()->where('post_id',$request->post_id)->count();

            $number_of_tips = \App\UserTip::where('post_id',$request->post_id)->count();

            $data->recent_comments = \App\PostComment::where('post_id',$request->post_id)->orderBy('post_comments.created_at', 'desc')->get();

            return view('admin.posts.dashboard')
                        ->with('page', 'posts') 
                        ->with('sub_page','posts-view') 
                        ->with('post', $post)
                        ->with('number_of_likes', $number_of_likes)
                        ->with('number_of_tips', $number_of_tips)
                        ->with('data',$data)
                        ->with('payment_data',$payment_data);

        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }

    }

    /**
     * @method posts_status
     *
     * @uses To update post status as DECLINED/APPROVED based on posts id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Post Id
     * 
     * @return response success/failure message
     *
     **/
    public function posts_status(Request $request) {

        try {

            DB::beginTransaction();

            $post = \App\Post::find($request->post_id);

            if(!$post) {

                throw new Exception(tr('post_not_found'), 101);

            }

            $post->status = $post->status ? DECLINED : APPROVED ;

            if($post->save()) {

                DB::commit();

                if($post->status == DECLINED) {

                    $email_data['subject'] = tr('post_decline_email' , Setting::get('site_name'));

                    $email_data['status'] = tr('declined');

                } else {

                    $email_data['subject'] = tr('post_approve_email' , Setting::get('site_name'));

                    $email_data['status'] = tr('approved');
                }

                $email_data['email']  = $post->user->email ?? "-";

                $email_data['name']  = $post->user->name ?? "-";

                $email_data['post_unique_id']  = $post->unique_id;

                $email_data['page'] = "emails.posts.status";

                $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                $message = $post->status ? tr('post_approve_success') : tr('post_decline_success');

                return redirect()->back()->with('flash_success', $message);
            }

            throw new Exception(tr('post_status_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.posts.index')->with('flash_error', $e->getMessage());

        }

    }


    /**
     * @method posts_publish
     *
     * @uses To publish the scheduled post
     *
     * @created sakthi
     *
     * @updated 
     *
     * @param object $request - Post Id
     * 
     * @return response success/failure message
     *
     **/
    public function posts_publish(Request $request) {

        try {

            DB::beginTransaction();

            $post = \App\Post::find($request->post_id);

            if(!$post) {

                throw new Exception(tr('post_not_found'), 101);

            }

            $post->is_published = YES ;
            
            $post->publish_time = date('Y-m-d H:i:s');

            if($post->save()) {

                DB::commit();

                return redirect()->back()->with('flash_success', tr('posts_publish_success'));
            }

            throw new Exception(tr('post_publish_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.posts.index')->with('flash_error', $e->getMessage());

        }

    }

    /**
     * @method post_albums_index()
     *
     * @uses Display the total posts albums index
     *
     * @created Akshata
     *
     * @updated
     *
     * @param -
     *
     * @return view page 
     */
    public function post_albums_index(Request $request) {

        $post_albums = \App\PostAlbum::orderBy('created_at','DESC')->paginate(10);

        return view('admin.post_albums.index')
                    ->with('page','post_albums')
                    ->with('post_albums', $post_albums);
    }

    /**
     * @method post_albums_view()
     *
     * @uses displays the specified post album details based on post album id
     *
     * @created Akshata 
     *
     * @updated 
     *
     * @param object $request - Post Album Id
     * 
     * @return View page
     *
     */
    public function post_albums_view(Request $request) {

        try {

            $post_album = \App\PostAlbum::find($request->post_album_id);

            if(!$post_album) {

                throw new Exception(tr('post_album_not_found'), 101);
            }

            $post_ids = explode(',', $post_album->post_ids);

            $posts = \App\Post::whereIn('posts.id', $post_ids)->get();

            return view('admin.post_albums.view')
                        ->with('page', 'post_albums') 
                        ->with('post_album' , $post_album)
                        ->with('posts',$posts);

        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }

    }

    /**
     * @method post_albums_delete()
     *
     * @uses delete the post album details based on post album id
     *
     * @created Akshata 
     *
     * @updated  
     *
     * @param object $request - Post Album Id
     * 
     * @return response of success/failure details with view page
     *
     */
    public function post_albums_delete(Request $request) {

        try {

            DB::begintransaction();

            $post_album = \App\Post::find($request->post_album_id);

            if(!$post_album) {

                throw new Exception(tr('post_album_not_found'), 101);                
            }

            if($post_album->delete()) {

                DB::commit();

                return redirect()->route('admin.post_albums.index')->with('flash_success',tr('post_album_deleted_success'));   

            } 

            throw new Exception(tr('post_album_delete_failed'));

        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       

    }

    /**
     * @method post_albums_status
     *
     * @uses To update post album status as DECLINED/APPROVED based on posts id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Post Album Id
     * 
     * @return response success/failure message
     *
     **/
    public function post_albums_status(Request $request) {

        try {

            DB::beginTransaction();

            $post_album = \App\PostAlbum::find($request->post_album_id);

            if(!$post_album) {

                throw new Exception(tr('post_album_not_found'), 101);

            }

            $post_album->status = $post_album->status ? DECLINED : APPROVED ;

            if($post_album->save()) {

                DB::commit();

                $message = $post_album->status ? tr('post_album_approve_success') : tr('post_album_decline_success');

                return redirect()->back()->with('flash_success', $message);
            }

            throw new Exception(tr('post_album_status_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.post_albums.index')->with('flash_error', $e->getMessage());

        }

    }

    /**
     * @method orders_index
     *
     * @uses Display list of orders
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Order Id
     * 
     * @return response success/failure message
     *
     **/
    public function orders_index(Request $request) {

        $base_query = \App\Order::where('unique_id','!=',NULL);

        if($request->status) {

            $base_query = $base_query->where('orders.status', $request->status);
        }

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query = $base_query
                            ->whereHas('userDetails',function($query) use($search_key) {

                                return $query->where('users.name','LIKE','%'.$search_key.'%');

                            })->orWhereHas('deliveryAddressDetails',function($query) use($search_key){

                                return $query->where('delivery_addresses.name','LIKE','%'.$search_key.'%');
                            }); 
        }

        if($request->user_id) {

            $base_query  = $base_query->where('user_id',$request->user_id);
        }

        $sub_page = 'orders-view';

        if($request->new_orders) {

            $base_query  = $base_query->latest('created_at');

            $sub_page = 'orders-new';
        }

        $orders = $base_query->paginate(10);

        return view('admin.orders.index')
                    ->with('page','orders')
                    ->with('sub_page',$sub_page)
                    ->with('orders',$orders);
    }


    /**
     * @method orders_view
     *
     * @uses Display the specified order details
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Order Id
     * 
     * @return response success/failure message
     *
     **/

    public function orders_view(Request $request) {

        try {

            $order = \App\Order::where('id',$request->order_id)->first();

            if(!$order) {

                throw new Exception(tr('order_not_found'), 1);

            }

            $order_products = \App\OrderProduct::where('order_id',$order->id)->get();

            $order_payment = \App\OrderPayment::where('order_id',$order->id)->first();

            $order = \App\Order::firstWhere('id',$request->order_id);

            return view('admin.orders.view')
                        ->with('page','orders')
                        ->with('sub_page','orders-view')
                        ->with('order', $order)
                        ->with('order_products', $order_products)
                        ->with('order_payment', $order_payment)
                        ->with('order', $order);

        } catch(Exception $e) {

            return redirect()->back()->with('flash_error',$e->getMessage());
        }
    }

    /**
     * @method delivery_address_index
     *
     * @uses Display list of all the delivery address
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param 
     * 
     * @return response success/failure message
     *
     **/
    public function delivery_address_index(Request $request) {

        $base_query = \App\DeliveryAddress::where('status',APPROVED);

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query = $base_query->whereHas('userDetails',function($query) use($search_key){

                return $query->where('users.name','LIKE','%'.$search_key.'%');

            })->orWhere('delivery_addresses.name','LIKE','%'.$search_key.'%')

            ->orWhere('delivery_addresses.state','LIKE','%'.$search_key.'%'); 
        }


        $user = \App\User::find($request->user_id) ?? '';

        if($request->user_id) {

            $base_query = $base_query->where('user_id',$request->user_id);
        }

        $delivery_addresses = $base_query->paginate($this->take);

        return view('admin.delivery_address.index')
                    ->with('page','delivery-address')
                    ->with('user',$user)
                    ->with('delivery_addresses',$delivery_addresses);
    }

    /**
     * @method delivery_address_view
     *
     * @uses Display the specified delivery address details
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - Delivery Address Id
     * 
     * @return response success/failure message
     *
     **/

    public function delivery_address_view(Request $request) {

        try {

            $delivery_address = \App\DeliveryAddress::where('id',$request->delivery_address_id)->first();

            if(!$delivery_address) {

                throw new Exception(tr('delvery_address_details_not_found'), 101);

            }

            return view('admin.delivery_address.view')
                    ->with('page','delivery-address')
                    ->with('delivery_address_details',$delivery_address);

        } catch(Exception $e) {

            return redirect()->back()->with('flash_error',$e->getMessage());
        }
    }


    /**
     * @method delivery_address_delete
     *
     * @uses Display list of all the delivery address
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param $object delivery_address_id
     * 
     * @return response success/failure message
     *
     **/

    public function delivery_address_delete(Request $request) {

        try {
            
            DB::begintransaction();

            $delivery_address = \App\DeliveryAddress::find($request->delivery_address_id);

            if(!$delivery_address) {

                throw new Exception(tr('delivery_address_details_not_found'), 101);                
            }

            if($delivery_address->delete()) {

                DB::commit();

                return redirect()->route('admin.delivery_address.index',['user_id'=>$delivery_address->user_id,'page'=>$request->page])->with('flash_success',tr('delivery_address_deleted_success'));   

            } 

            throw new Exception(tr('delivery_address_delete_failed'));

        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       

    }

    /**
     * @method Bookmarks_index
     *
     * @uses Display list of all the bookmarks
     *
     * @created Sakthi
     *
     * @updated 
     *
     * @param 
     * 
     * @return response success/failure message
     *
     **/
    public function post_bookmarks_index(Request $request) {

        $base_query = \App\PostBookmark::Approved()->orderBy('post_bookmarks.created_at', 'desc');


        if($request->search_key) {
            $search_key = $request->search_key;

            $base_query = $base_query->whereHas('post',function($query) use($search_key){

                return $query->where('posts.content','LIKE','%'.$search_key.'%');

            });

        }

        
        $user = \App\User::find($request->user_id) ?? '';

        if($request->user_id) {

            $base_query = $base_query->where('user_id',$request->user_id);
        }

        $post_bookmarks = $base_query->paginate($this->take);

        return view('admin.bookmarks.index')
                    ->with('page','post_bookmarks')
                    ->with('sub_page','users-view')
                    ->with('user',$user)
                    ->with('post_bookmarks',$post_bookmarks);
    }

    /**
     * @method bookmarks_delete
     *
     * @uses Display list of all the bookmarks
     *
     * @created Sakthi
     *
     * @updated 
     *
     * @param 
     * 
     * @return response success/failure message
     *
     **/
    public function post_bookmarks_delete(Request $request) {

        try {

            DB::begintransaction();

            $post_bookmark = \App\PostBookmark::find($request->post_bookmark_id);

            if(!$post_bookmark) {

                throw new Exception(tr('post_bookmark_not_found'), 101);                
            }

            $post_bookmark->where('user_id',$request->user_id);

            if($post_bookmark->delete()) {

                DB::commit();

                return redirect()->route('admin.bookmarks.index',['page'=>$request->page,'user_id'=>$post_bookmark->user_id])->with('flash_success',tr('bookmark_deleted_success'));   

            } 

            throw new Exception(tr('bookmark_delete_failed'));

        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       

    }

    /**
     * @method bookmarks_view
     *
     * @uses view the bookmark
     *
     * @created Sakthi
     *
     * @updated 
     *
     * @param 
     * 
     * @return response success/failure message
     *
     **/
    public function post_bookmarks_view(Request $request) {

        try {

            $post_bookmark = \App\PostBookmark::where('id', $request->post_bookmark_id)->where('user_id',$request->user_id)->first();

            if(!$post_bookmark) {

                throw new Exception(tr('bookmark_details_not_found'), 101);

            }

            return view('admin.bookmarks.view')
                    ->with('page','bookmarks')
                    ->with('post_bookmark', $post_bookmark);

        } catch(Exception $e) {

            return redirect()->back()->with('flash_error',$e->getMessage());
        }

    }       



    /**
     * @method post_comments
     *
     * @uses List of comments for particular post
     *
     * @created Sakthi
     *
     * @updated 
     *
     * @param 
     * 
     * @return response success/failure message
     *
     **/
    public function post_comments(Request $request) {

        $base_query = \App\PostComment::Approved()->orderBy('post_comments.created_at', 'desc');

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query = $base_query->whereHas('user',function($query) use($search_key){

                return $query->where('users.name','LIKE','%'.$search_key.'%');

            });

        }
        if($request->post_id){
            $base_query->where('post_comments.post_id',  $request->post_id);
        }

        $post_comments = $base_query->paginate(10);

        $post = $request->post_id ? \App\Post::find($request->post_id) : '';

        return view('admin.posts.comments')
                ->with('page', 'posts')
                ->with('sub_page', 'posts-view')
                ->with('post_id', $request->post_id)
                ->with('post', $post)
                ->with('post_comments', $post_comments);

      
    }

    /**
     * @method post_comment_delete
     *
     * @uses delete particular comment
     *
     * @created Sakthi
     *
     * @updated 
     *
     * @param 
     * 
     * @return response success/failure message
     *
     **/
    public function post_comment_delete(Request $request) {

        try {

            DB::begintransaction();

            $post_comment = \App\PostComment::find($request->comment_id);

            if(!$post_comment) {

                throw new Exception(tr('post_comment_not_found'), 101);                
            }

            $post_comment->where('post_id',$request->post_id);

            if($post_comment->delete()) {

                DB::commit();

                return redirect()->back()->with('flash_success',tr('post_comment_deleted'));   

            } 

            throw new Exception(tr('post_comment_delete_failed'));

        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }   
    }

    /**
     * @method fav_users
     *
     * @uses List of fav users
     *
     * @created Sakthi
     *
     * @updated 
     *
     * @param 
     * 
     * @return response success/failure message
     *
     **/
    public function fav_users(Request $request) {

        $base_query = \App\FavUser::Approved()->orderBy('fav_users.created_at', 'desc');

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query = $base_query->whereHas('user',function($query) use($search_key){

                return $query->where('users.name','LIKE','%'.$search_key.'%');

            });

        }

        $user = \App\User::find($request->user_id)??'';

        if($request->user_id){

            $base_query->where('user_id', $request->user_id);
        }

        $fav_users = $base_query->paginate(10);

        return view('admin.fav_users.index')
                    ->with('page','fav_users')
                    ->with('user',$user)
                    ->with('fav_users',$fav_users);
    }

    /**
     * @method fav_users_delete
     *
     * @uses List of fav users
     *
     * @created Sakthi
     *
     * @updated 
     *
     * @param 
     * 
     * @return response success/failure message
     *
     **/
    public function fav_users_delete(Request $request) {

        try {

            DB::begintransaction();

            $fav_user = \App\FavUser::find($request->fav_user_id);

            if(!$fav_user) {

                throw new Exception(tr('fav_user_not_found'), 101);                
            }

            $fav_user->where('fav_user_id',$request->user_id);

            if($fav_user->delete()) {

                DB::commit();

                return redirect()->back()->with('flash_success',tr('fav_user_deleted'));   

            } 

            throw new Exception(tr('fav_user_delete_failed'));

        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }   
    }

    /**
     * @method post_likes
     *
     * @uses List of liked post for users
     *
     * @created Sakthi
     *
     * @updated 
     *
     * @param 
     * 
     * @return response success/failure message
     *
     **/
    public function post_likes(Request $request) {

        $base_query = \App\PostLike::Approved()->orderBy('post_likes.created_at', 'desc');

        if($request->search_key) {

            $search_key = $request->search_key;

            $base_query = $base_query->whereHas('postUser',function($query) use($search_key){

                return $query->where('users.name','LIKE','%'.$search_key.'%');

            });
        }

        $user = \App\User::find($request->user_id) ?? '';

        if($request->user_id){

            $base_query->where('user_id', $request->user_id);
        }

        $post_likes = $base_query->paginate($this->take);

        return view('admin.post_likes.index')
                    ->with('page','post_likes')
                    ->with('user_id',$request->user_id)
                    ->with('user',$user)
                    ->with('post_likes',$post_likes); 
     }


    /**
     * @method post_likes_delete
     *
     * @uses remove liked post
     *
     * @created Sakthi
     *
     * @updated 
     *
     * @param 
     * 
     * @return response success/failure message
     *
     **/
    public function post_likes_delete(Request $request) {

        try {

            DB::begintransaction();

            $post_likes = \App\PostLike::find($request->post_like_id);

            if(!$post_likes) {

                throw new Exception(tr('post_not_found'), 101);                
            }

            $post_likes->where('user_id',$request->user_id);

            if($post_likes->delete()) {

                DB::commit();

                return redirect()->route('admin.post_likes.index',['user_id'=>$request->user_id ?? '','page'=>$request->page])->with('flash_success',tr('like_post_deleted'));   

            } 

            throw new Exception(tr('like_post_delete_failed'));

        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }   
    }


}
