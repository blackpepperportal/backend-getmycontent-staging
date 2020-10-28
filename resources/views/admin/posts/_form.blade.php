<section id="basic-form-layouts">
    
    <div class="row match-height">
    
        <div class="col-lg-12">

            <div class="card">
                
                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title" id="basic-layout-form">{{ $post_details->id ? tr('edit_post') : tr('create_post') }}</h4>

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

                                    <input type="hidden" name="post_id" id="post_id" value="{{ $post_details->id}}">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user_name">{{ tr('content') }}*</label>

                                             <textarea id="ckeditor" rows="5" class="form-control" name="content" placeholder="{{ tr('content') }}">{{$post_details->content ?: old('content')}}</textarea>

                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user_name">{{ tr('amount') }}</label>
                                            <input type="number" id="amount" name="amount" class="form-control" placeholder="{{ tr('amount') }}" value="{{ $post_details->amount ?: old('amount') }}" >
                                        </div>
                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                          <label for="page">
                                                {{tr('select_publish_type')}}
                                                <span class="required" aria-required="true"> <span class="admin-required">*</span> </span>
                                            </label>
                                            
                                            <select class="form-control select2" name="publish_type" required>
                                                <option>{{tr('select_publish_type')}}</option>

                                                    <option value="{{PUBLISHED}}" @if(PUBLISHED == $post_details->is_published) selected="true" @endif>{{ tr('now') }}</option>

                                                     <option value="{{UNPUBLISHED}}" @if(UNPUBLISHED == $post_details->is_published) selected="true" @endif>{{ tr('schedule') }}</option>

                                            </select>
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

