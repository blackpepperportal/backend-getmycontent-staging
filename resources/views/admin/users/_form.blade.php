<section id="basic-form-layouts">
    
    <div class="row match-height">
    
        <div class="col-lg-12">

            <div class="card">
                
                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title" id="basic-layout-form">{{ tr('user') }}</h4>

                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                    <div class="heading-elements">
                        <a href="{{route('admin.users.index') }}" class="btn btn-primary"><i class="ft-user icon-left"></i>{{ tr('view_users') }}</a>
                    </div>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body">
                    
                        <div class="card-text">

                        </div>

                        <form class="form-horizontal" action="{{ (Setting::get('is_demo_control_enabled') == YES) ? '#' : route('admin.users.save') }}" method="POST" enctype="multipart/form-data" role="form">
                           
                            @csrf
                          
                            <div class="form-body">

                                <div class="row">

                                    <input type="hidden" name="user_id" id="user_id" value="{{ $user_details->id}}">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user_name">{{ tr('name') }}*</label>
                                            <input type="text" id="name" name="name" class="form-control" placeholder="{{ tr('name') }}" value="{{ $user_details->name ?: old('name') }}" required onkeydown="return alphaOnly(event);">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">{{tr('email')}}*</label>
                                            <input type="text" id="email" name="email" class="form-control" placeholder="E-mail" value="{{ $user_details->email ?: old('email') }}" required>
                                        </div>
                                    </div>

                                </div>

                                @if(!$user_details->id)
                                
                                <div class="row">

                                    <div class="col-md-6">                    
                                        <div class="form-group">
                                            <label for="password" class="">{{ tr('password') }} *</label>
                                            <input type="password" minlength="6" required name="password" class="form-control" id="password" placeholder="{{ tr('password') }}" >
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">  
                                            <label for="confirm-password" class="">{{ tr('confirm_password') }} *</label>
                                          <input type="password" minlength="6" required name="password_confirmation" class="form-control" id="confirm-password" placeholder="{{ tr('confirm_password') }}">
                                        </div>
                                    </div>
                                
                                </div>

                                @endif

                                <div class="form-group">
                                    <label for="description">{{ tr('description') }}</label>
                                    <div id="editor">
                                        <textarea rows="5" class="form-control" name="description" placeholder="{{ tr('description') }}">{{ $user_details->description ? $user_details->description :old('description') }}</textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                        <label>{{ tr('select_picture') }}</label>
                                            <label id="user_picture" class="file center-block">
                                                <input type="file" id="picture" name="picture" accept="image/png,image/jpeg" onchange="loadFile(this,'image_preview')">
                                                 <img id="image_preview" class="img-thumbnail img-fluid" style="width: 100px;margin: 10px;height: 100px; " src="{{ $user_details->picture ? $user_details->picture : asset('placeholder.png') }}">
                                            <span class="file-custom"></span>
                                            </label>                                
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

