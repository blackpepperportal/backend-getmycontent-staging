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

}
