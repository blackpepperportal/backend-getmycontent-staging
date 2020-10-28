<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper, App\Helpers\EnvEditorHelper;

use DB, Hash, Setting, Auth, Validator, Exception, Enveditor;

use App\Jobs\SendEmailJob;

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

        ->whereHas('getuserDetails', function($q) use ($search_key) {

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
        ->with('posts' , $posts);
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

        $post_details = \App\Post::find($request->post_id);

        if(!$post_details) { 

            throw new Exception(tr('post_not_found'), 101);                
        }

        $payment_data = new \stdClass;

        $payment_data->total_earnings = \App\PostPayment::where('post_id',$request->post_id)->sum('paid_amount');

        $payment_data->current_month_earnings = \App\PostPayment::where('post_id',$request->post_id)->whereMonth('paid_date',date('m'))->sum('paid_amount');

        $payment_data->today_earnings = \App\PostPayment::where('post_id',$request->payment_id)->whereDate('paid_date',today())->sum('paid_amount');


        return view('admin.posts.view')
        ->with('page', 'posts') 
        ->with('sub_page','posts-view') 
        ->with('post_details' , $post_details)
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

        $post_details = \App\Post::find($request->post_id);

        if(!$post_details) {

            throw new Exception(tr('post_not_found'), 101);                
        }

        if($post_details->delete()) {

            DB::commit();

            return redirect()->route('admin.posts.index')->with('flash_success', tr('post_deleted_success'));   

        } 

        throw new Exception(tr('post_delete_failed'));

    } catch(Exception $e){

        DB::rollback();

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

        $post_details = \App\Post::find($request->post_id);

        if(!$post_details) {

            throw new Exception(tr('post_not_found'), 101);

        }

        $post_details->status = $post_details->status ? DECLINED : APPROVED ;

        if($post_details->save()) {

            DB::commit();

            if($post_details->status == DECLINED) {

                $email_data['subject'] = tr('post_decline_email' , Setting::get('site_name'));

                $email_data['status'] = tr('declined');

            } else {

                $email_data['subject'] = tr('post_approve_email' , Setting::get('site_name'));

                $email_data['status'] = tr('approved');
            }

            $email_data['email']  = $post_details->getuserDetails->email ?? "-";

            $email_data['name']  = $post_details->getuserDetails->name ?? "-";

            $email_data['post_unique_id']  = $post_details->unique_id;

            $email_data['page'] = "emails.posts.status";

            $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

            $message = $post_details->status ? tr('post_approve_success') : tr('post_decline_success');

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
        
        $post->publish_time = now() ;

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
    ->with('post_albums' , $post_albums);
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

        $post_album_details = \App\PostAlbum::find($request->post_album_id);

        if(!$post_album_details) {

            throw new Exception(tr('post_album_not_found'), 101);
        }

        $post_ids = explode(',', $post_album_details->post_ids);

        $posts = \App\Post::whereIn('posts.id', $post_ids)->get();

        return view('admin.post_albums.view')
        ->with('page', 'post_albums') 
        ->with('post_album_details' , $post_album_details)
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

        $post_album_details = \App\Post::find($request->post_album_id);

        if(!$post_album_details) {

            throw new Exception(tr('post_album_not_found'), 101);                
        }

        if($post_album_details->delete()) {

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

        $post_album_details = \App\PostAlbum::find($request->post_id);

        if(!$post_album_details) {

            throw new Exception(tr('post_album_not_found'), 101);

        }

        $post_album_details->status = $post_album_details->status ? DECLINED : APPROVED ;

        if($post_album_details->save()) {

            DB::commit();

            $message = $post_album_details->status ? tr('post_album_approve_success') : tr('post_album_decline_success');

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

        $order_details = \App\Order::where('id',$request->order_id)->first();

        if(!$order_details) {

            throw new Exception(tr('order_details_not_found'), 1);

        }

        $order_products = \App\OrderProduct::where('order_id',$order_details->id)->get();

        $order_payment_details = \App\OrderPayment::where('order_id',$order_details->id)->first();

        return view('admin.orders.view')
        ->with('page','orders')
        ->with('sub_page','orders-view')
        ->with('order_details',$order_details)
        ->with('order_products',$order_products)
        ->with('order_payment_details',$order_payment_details);

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

    if($request->user_id) {

        $base_query = $base_query->where('user_id',$request->user_id);
    }

    $delivery_addresses = $base_query->paginate(10);

    return view('admin.delivery_address.index')
    ->with('page','delivery-address')
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

        $delivery_address_details = \App\DeliveryAddress::where('id',$request->delivery_address_id)->first();

        if(!$delivery_address_details) {

            throw new Exception(tr('delvery_address_details_not_found'), 101);

        }

        return view('admin.delivery_address.view')
        ->with('page','delivery-address')
        ->with('delivery_address_details',$delivery_address_details);

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

        $delivery_address_details = \App\DeliveryAddress::find($request->delivery_address_id);

        if(!$delivery_address_details) {

            throw new Exception(tr('delivery_address_details_not_found'), 101);                
        }

        if($delivery_address_details->delete()) {

            DB::commit();

            return redirect()->route('admin.delivery_address.index')->with('flash_success',tr('delivery_address_deleted_success'));   

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


    if($request->user_id) {

        $base_query = $base_query->where('user_id',$request->user_id);
    }

    $bookmarks = $base_query->paginate(10);

    return view('admin.bookmarks.index')
    ->with('page','post_bookmarks')
    ->with('post_bookmarks',$bookmarks);
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

        $bookmark_details = \App\PostBookmark::find($request->bookmark_id);

        if(!$bookmark_details) {

            throw new Exception(tr('post_bookmark_not_found'), 101);                
        }

        $bookmark_details->where('user_id',$request->user_id);

        if($bookmark_details->delete()) {

            DB::commit();

            return redirect()->back()->with('flash_success',tr('bookmark_deleted_success'));   

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

        $bookmarks_details = \App\PostBookmark::where('id',$request->bookmark_id)->where('user_id',$request->user_id)->first();


        if(!$bookmarks_details) {

            throw new Exception(tr('bookmark_details_not_found'), 101);

        }

        return view('admin.bookmarks.view')
        ->with('page','bookmarks')
        ->with('post_bookmarks',$bookmarks_details);

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

        return view('admin.posts.comments')
        ->with('page','post_comments')
        ->with('post_id',$request->post_id)
        ->with('post_comments',$post_comments);

  
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
    if($request->user_id){
        $base_query->where('user_id', $request->user_id);
    }

    $fav_users = $base_query->paginate(10);

    return view('admin.fav_users.index')
    ->with('page','fav_users')
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

    if($request->user_id){
        $base_query->where('user_id', $request->user_id);
    }

    $post_likes = $base_query->paginate(10);

    return view('admin.post_likes.index')
    ->with('page','post_likes')
    ->with('user_id',$request->user_id)
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

        $post_likes = \App\PostLike::find($request->post_id);

        if(!$post_likes) {

            throw new Exception(tr('post_not_found'), 101);                
        }

        $post_likes->where('user_id',$request->user_id);

        if($post_likes->delete()) {

            DB::commit();

            return redirect()->back()->with('flash_success',tr('like_post_deleted'));   

        } 

        throw new Exception(tr('like_post_delete_failed'));

    } catch(Exception $e){

        DB::rollback();

        return redirect()->back()->with('flash_error', $e->getMessage());

    }   
}


}
