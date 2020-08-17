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

                ->whereHas('getStardomDetails', function($q) use ($search_key) {

                    return $q->Where('stardoms.name','LIKE','%'.$search_key.'%');

                })->orWhere('posts.content','LIKE','%'.$search_key.'%');
                        
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

       
        $sub_page = 'posts-view';

        if($request->scheduled) {

            $sub_page = 'scheduled-posts';

            $base_query = $base_query->where('publish_time','!=',NULL);
        }

        $posts = $base_query->paginate(10);

        return view('admin.posts.index')
                    ->with('main_page','posts-crud')
                    ->with('page','posts')
                    ->with('sub_page' , $sub_page)
                    ->with('posts' , $posts);
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

            return view('admin.posts.view')
                        ->with('main_page','posts-crud')
                        ->with('page', 'posts') 
                        ->with('sub_page','posts-index') 
                        ->with('post_details' , $post_details);
            
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

            if($stardom_details->delete()) {

                DB::commit();

                return redirect()->route('admin.posts.index')->with('flash_success',tr('post_deleted_success'));   

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
                    ->with('main_page','post_albums')
                    ->with('page','post_albums')
                    ->with('sub_page' , 'post_albums-view')
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
                        ->with('main_page','post_albums')
                        ->with('page', 'post_albums') 
                        ->with('sub_page','post_albums-view') 
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

            if($stardom_details->delete()) {

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

}
