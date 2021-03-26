<section id="basic-form-layouts">
    
    <div class="row match-height">
    
        <div class="col-lg-12">

            <div class="card">
                
                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title" id="basic-layout-form">{{ $post->id ? tr('edit_post') : tr('create_post') }}</h4>

                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                    <div class="heading-elements">
                        <a href="{{route('admin.posts.index') }}" class="btn btn-primary"><i class="ft-eye icon-left"></i>{{ tr('view_posts') }}</a>
                    </div>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body">
                    
                        <div class="card-text">

                        </div>

                        <form class="form-horizontal" action="{{ (Setting::get('is_demo_control_enabled') == YES) ? '#' : route('admin.posts.save') }}" method="POST" enctype="multipart/form-data" role="form">
                           
                            @csrf
                          
                            <div class="form-body">


                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="page">
                                                {{tr('user_name')}}
                                                <span class="required" aria-required="true"> <span class="admin-required">*</span> </span>
                                            </label>

                                            <select class="form-control select2" name="user_id" required>

                                                <option>{{tr('select_user_name')}}</option>
                                                @foreach($users as $user)
                                                    <option value="{{$user->id}}" @if($user->id == $post->user_id) selected="true" @endif>
                                                        {{$user->name}}
                                                    </option>
                                                @endforeach
                                            
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="page">
                                                {{tr('select_image_type')}}
                                                <span class="required" aria-required="true"> <span class="admin-required">*</span> </span>
                                            </label><br>

                                            <input type="radio" onclick="select_image_type();" name="file_type" value="{{POSTS_IMAGE}}" {{ ($post->is_published  == POSTS_IMAGE)? "checked" : "" }}   ><label class="ml-1" for="{{POSTS_IMAGE}}"> {{tr('image')}} </label>

                                            <input class="ml-1" type="radio"  onclick="select_image_type();" name="file_type" value="{{POSTS_VIDEO}}"  {{ ($post->is_published  == POSTS_VIDEO)? "checked" : "" }} ><label class="ml-1" for="{{POSTS_VIDEO}}"> {{tr('video')}} </label>&nbsp;
                                          
                                        </div>
                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-6 preview_file" style="display: none;">
                                        
                                        <div class="form-group">
                                            <label for="page">{{tr('preview_file')}}</label>
                                            <input type="file" class="form-control" name="preview_file" accept="image/*" />
                                        </div>

                                    </div>

                                   <div class="col-md-6">
                                        
                                        <div class="form-group">
                                            <label for="page">{{tr('upload_files')}}</label>
                                            <input type="file" class="form-control" name="post_files" accept="image/*,video/*" />
                                        </div>

                                    </div>

                                </div>

                                <div class="row">

                                    <input type="hidden" name="post_id" id="post_id" value="{{ $post->id}}">

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="user_name">{{ tr('content') }}*</label>
                                            <textarea name="content" class="form-control">{{ $post->content ?: old('content') }}</textarea>
                                        </div>
                                    </div>

                                </div>


                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user_name">{{ tr('amount') }}</label>
                                            <input type="number" id="amount" name="amount" class="form-control" placeholder="{{ tr('amount') }}" value="{{ $post->amount ?: old('amount') }}" >
                                        </div>
                                    </div>

                                </div>
                                
                                
                            </div>
                          
                            <div class="form-actions">

                                <div class="pull-right">
                                
                                    <button type="reset" class="btn btn-warning mr-1">
                                        <i class="ft-x"></i> {{ tr('reset') }} 
                                    </button>

                                    <button type="submit" class="btn btn-primary" @if(Setting::get('is_demo_control_enabled') == YES) disabled @endif ><i class="fa fa-check-square-o"></i>{{ tr('submit') }}</button>
                                
                                </div>

                                <div class="clearfix"></div>

                            </div>

                        </form>
                        
                    </div>
                
                </div>

            </div>
        
        </div>
    
    </div>

</section>

