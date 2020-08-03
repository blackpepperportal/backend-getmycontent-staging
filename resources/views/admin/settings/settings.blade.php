@extends('layouts.admin') 

@section('title', tr('settings')) 

@section('content-header', tr('settings'))

@section('breadcrumb_left')

    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ tr('home') }}</a>
    </li>
    <li class="breadcrumb-item active">{{ tr('settings') }}</a>
    </li>

@endsection 

@section('content')

<div class="col-xl-12 col-lg-12">

    <div class="card">
        
        <div class="card-content">

            <div class="card-body">
                
                <ul class="nav nav-tabs nav-linetriangle no-hover-bg nav-justified">

                    <li class="nav-item">
                        <a class="nav-link active" id="site_settings" data-toggle="tab" aria-controls="site_settings_tab" href="#site_settings_tab" aria-expanded="true">
                            <i class="fa fa-setting"></i>  
                            {{ tr('site_settings') }}
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link " id="social_settings" data-toggle="tab" aria-controls="social_settings_tab" href="#social_settings_tab" aria-expanded="false">
                            {{ tr('social_settings') }}
                        </a>
                    </li>


                    <li class="nav-item">
                        <a class="nav-link" id="email_settings" data-toggle="tab" aria-controls="email_settings_tab" href="#email_settings_tab" aria-expanded="false">
                            {{ tr('email_settings') }}
                        <a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="social_and_app_settings" data-toggle="tab" aria-controls="social_and_app_settings_tab" href="#social_and_app_settings_tab" aria-expanded="false">
                            {{ tr('social_and_app_settings') }}
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="other_settings" data-toggle="tab" aria-controls="other_settings_tab" href="#other_settings_tab" aria-expanded="false">
                            {{ tr('other_settings') }}
                        </a>
                    </li>

                </ul>

                <div class="tab-content px-1 pt-1">

                    <!-- SITE SETTINGS START -->

                    <div role="tabpanel" class="tab-pane active" id="site_settings_tab" aria-expanded="true" aria-labelledby="site_settings">

                        <div class="card-body">

                            <form class="form" action="{{ (Setting::get('is_demo_control_enabled') ==  YES ) ? '#' : route('admin.settings.save') }}" method="POST" enctype="multipart/form-data" role="form">

                            @csrf

                                <div class="form-body">

                                    <div class="form-group">
                                        <label for="sitename">{{ tr('site_name') }}</label>
                                        <input type="text" class="form-control" name="site_name" value="{{ Setting::get('site_name')  }}" id="sitename" placeholder="{{ tr('enter_site_name') }}">
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="site_logo">{{ tr('site_logo') }}</label>

                                            <br> 

                                            @if(Setting::get('site_logo'))
                                            <img style="height: 50px; width:75px;margin-bottom: 15px; border-radius:2em;" src="{{ Setting::get('site_logo') }}"> 
                                            @endif

                                            <input type="file" id="site_logo" name="site_logo" accept="image/png, image/jpeg">
                                            <p class="help-block">{{ tr('image_notes') }}</p>

                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="site_icon">{{ tr('site_icon') }}</label>
                                                <br> 

                                                @if(Setting::get('site_icon'))
                                                    <img style="height: 50px; width:75px; margin-bottom: 15px; border-radius:2em;" src="{{ Setting::get('site_icon') }}"> 
                                                @endif

                                                <input type="file" id="site_icon" name="site_icon" accept="image/png, image/jpeg">
                                                <p class="help-block">{{ tr('image_notes') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                
                                </div>

                                <div class="form-actions">

                                    <div class="pull-right">

                                        <button type="reset"  class="btn btn-warning mr-1 ">
                                            <i class="ft-x "></i> {{ tr('reset') }}
                                        </button>
                                        
                                        <button type="submit" class="btn btn-primary" @if(Setting::get('is_demo_control_enabled') == YES) disabled  @endif ><i class="fa fa-check-square-o"></i>{{ tr('submit') }}</button>

                                    </div>

                                </div>

                            </form>

                        </div>

                    </div>

                    <div class="tab-pane" id="social_settings_tab" aria-labelledby="social_settings">

                        <form action="{{ (Setting::get('is_demo_control_enabled') ==  YES) ? '' : route('admin.env-settings.save') }}" method="POST" enctype="multipart/form-data" role="form">

                        @csrf
                            <div class="box-body">

                                <div class="row">

                                    <div class="col-md-12">
                                        <h3 class="settings-sub-header text-uppercase">{{ tr('fb_settings') }}</h3>
                                        <hr>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="fb_client_id">{{ tr('FB_CLIENT_ID') }}</label>
                                            <input type="text" class="form-control" name="FB_CLIENT_ID" id="fb_client_id" placeholder="{{ tr('FB_CLIENT_ID') }}" value="{{ $env_values['FB_CLIENT_ID']}}">
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="fb_client_secret">{{ tr('FB_CLIENT_SECRET') }}</label>
                                            <input type="text" class="form-control" name="FB_CLIENT_SECRET" id="fb_client_secret" placeholder="{{ tr('FB_CLIENT_SECRET') }}" value="{{ $env_values['FB_CLIENT_SECRET']}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="fb_call_back">{{ tr('FB_CALL_BACK') }}</label>
                                            <input type="text" class="form-control" name="FB_CALL_BACK" id="fb_call_back" placeholder="{{ tr('FB_CALL_BACK') }}" value="{{ $env_values['FB_CALL_BACK']}}">
                                        </div>
                                    </div>

                                    <div class="clearfix"></div>

                                    
                                    <div class="clearfix"></div>

                                    <div class="col-md-12">
                                        <h3 class="settings-sub-header text-uppercase">{{ tr('google_settings') }}</h3>
                                        <hr>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="google_client_id">{{ tr('GOOGLE_CLIENT_ID') }}</label>
                                            <input type="text" class="form-control" name="GOOGLE_CLIENT_ID" id="google_client_id" placeholder="{{ tr('GOOGLE_CLIENT_ID') }}" value="{{ $env_values['GOOGLE_CLIENT_ID']}}">
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="google_client_secret">{{ tr('GOOGLE_CLIENT_SECRET') }}</label>
                                            <input type="text" class="form-control" name="GOOGLE_CLIENT_SECRET" id="google_client_secret" placeholder="{{ tr('GOOGLE_CLIENT_SECRET') }}" value="{{ $env_values['GOOGLE_CLIENT_SECRET']}}">
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="google_call_back">{{ tr('GOOGLE_CALL_BACK') }}</label>
                                            <input type="text" class="form-control" name="GOOGLE_CALL_BACK" id="google_call_back" placeholder="{{ tr('GOOGLE_CALL_BACK') }}" value="{{ $env_values['GOOGLE_CALL_BACK']}}">
                                        </div>
                                    </div>

                                     <div class="col-md-12">
                                        <h3 class="settings-sub-header text-uppercase">{{ tr('fcm_settings') }}</h3>
                                        <hr>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">

                                            <label for="FCM_SERVER_KEY">{{tr('FCM_SERVER_KEY')}}</label>

                                            <input type="text" class="form-control" name="FCM_SERVER_KEY" id="FCM_SERVER_KEY"
                                            value="{{envfile('FCM_SERVER_KEY')}}" placeholder="{{tr('FCM_SERVER_KEY')}}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">

                                            <label for="FCM_SENDER_ID">{{tr('FCM_SENDER_ID')}}</label>

                                            <input type="text" class="form-control" name="FCM_SENDER_ID" id="FCM_SENDER_ID"
                                            value="{{envfile('FCM_SENDER_ID')}}" placeholder="{{tr('FCM_SENDER_ID')}}">
                                        </div>
                                    </div>


                                    <div class='clearfix'></div>

                                </div>

                            </div>


                            <div class="form-actions">

                                <div class="pull-right">

                                    <button type="reset"  class="btn btn-warning mr-1 ">
                                        <i class="ft-x "></i> {{ tr('reset') }}
                                    </button>

                                    
                                    <button type="submit" class="btn btn-primary" @if(Setting::get('is_demo_control_enabled') == YES) disabled  @endif ><i class="fa fa-check-square-o"></i>{{ tr('submit') }}</button>
                                </div>

                            </div>
                            
                            <div class="clearfix"></div>

                        </form>
                        <!-- <p>Sugar plum tootsica.</p> -->
                    
                    </div>

                    <!-- Social SETTINGS END -->


                    <!-- Email SETTINGS START -->

                    <div class="tab-pane" id="email_settings_tab" aria-labelledby="email_settings">

                        <form action="{{ (Setting::get('is_demo_control_enabled') == YES) ? '#' : route('admin.env-settings.save') }}" method="POST" enctype="multipart/form-data" role="form">

                            @csrf
                            
                            <div class="form-body">

                                <div class="row">

                                    <div class="col-md-12">

                                        <h3 class="settings-sub-header text-uppercase">{{ tr('email_settings') }}</h3>

                                        <hr>

                                    </div>

                                    <div class="col-md-6 col-sm-6">

                                        <div class="form-group">
                                            <label for="MAIL_DRIVER">{{ tr('MAIL_DRIVER') }} *</label>
                                            <p class="txt-default m-0">{{ tr('mail_driver_note') }}</p>
                                            <input type="text" class="form-control" id="MAIL_DRIVER" name="MAIL_DRIVER" placeholder="Enter {{ tr('MAIL_DRIVER') }}" value="{{ old('MAIL_DRIVER') ? old('MAIL_DRIVER') : $env_values['MAIL_DRIVER'] }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="MAIL_HOST">{{ tr('MAIL_HOST') }} *</label>
                                            <p class="txt-default m-0">{{ tr('mail_host_note') }}</p>

                                            <input type="text" class="form-control" id="MAIL_HOST" name="MAIL_HOST" placeholder="Enter {{ tr('MAIL_HOST') }}" value="{{ old('MAIL_HOST') ? old('MAIL_HOST') : $env_values['MAIL_HOST']}}">
                                        </div>

                                        <div class="form-group">
                                            <label for="MAIL_PORT">{{ tr('MAIL_PORT') }} *</label>

                                            <p class="txt-default m-0">{{ tr('mail_port_note') }}</p>

                                            <input type="text" class="form-control" id="MAIL_PORT" name="MAIL_PORT" placeholder="Enter {{ tr('MAIL_PORT') }}" value="{{ old('MAIL_PORT') ? old('MAIL_PORT') : $env_values['MAIL_PORT']}}">
                                        </div>
                                    
                                    </div>

                                    <div class="col-md-6 col-sm-6">

                                        <div class="form-group">
                                            <label for="MAIL_USERNAME">{{ tr('MAIL_USERNAME') }} *</label>
                                            
                                            <p class="txt-default m-0">{{ tr('mail_username_note') }}</p>

                                            <input type="text" class="form-control" id="MAIL_USERNAME" name="MAIL_USERNAME" placeholder="Enter {{ tr('MAIL_USERNAME') }}" value="{{ old('MAIL_USERNAME') ? old('MAIL_USERNAME') : $env_values['MAIL_USERNAME']}}">
                                        </div>

                                        <div class="form-group">

                                            <label for="MAIL_PASSWORD">{{ tr('MAIL_PASSWORD') }} *</label>
                                            
                                            <p class="txt-default m-0">{{ tr('mail_password_note') }}</p>

                                            <input type="password" class="form-control" id="MAIL_PASSWORD" name="MAIL_PASSWORD" placeholder="Enter {{ tr('MAIL_PASSWORD') }}" >
                                        </div>

                                        <div class="form-group">
                                            <label for="MAIL_ENCRYPTION">{{ tr('MAIL_ENCRYPTION') }} *</label>
                                            
                                            <p class="txt-default m-0">{{ tr('mail_encryption_note') }}</p>

                                            <input type="text" class="form-control" id="MAIL_ENCRYPTION" name="MAIL_ENCRYPTION" placeholder="Enter {{ tr('MAIL_ENCRYPTION') }}" value="{{ old('MAIL_ENCRYPTION') ? old('MAIL_ENCRYPTION') : $env_values['MAIL_ENCRYPTION']}}">
                                        </div>
                                        
                                    </div>

                                </div>

                            </div>

                            <div class="form-actions">

                                <div class="pull-right">

                                    <button type="reset"  class="btn btn-warning mr-1 ">
                                        <i class="ft-x "></i> {{ tr('reset') }}
                                    </button>
                                    
                                    <button type="submit" class="btn btn-primary" @if(Setting::get('is_demo_control_enabled') == YES) disabled  @endif ><i class="fa fa-check-square-o"></i>{{ tr('submit') }}</button>

                                </div>

                            </div>
                            
                            <div class="clearfix"></div>

                        </form>
                        <!-- <p>Sugar plum tootsica.</p> -->
                    
                    </div>

                    <!-- Email SETTINGS END -->

                    <!-- social_and_app_settings SETTINGS start -->

                    <div class="tab-pane" id="social_and_app_settings_tab" aria-labelledby="social_and_app_settings">

                        <form action="{{ (Setting::get('is_demo_control_enabled') == YES) ? '#' : route('admin.settings.save') }}" method="POST" enctype="multipart/form-data" role="form">

                        @csrf
                            
                            <div class="form-body">

                                <div class="row">

                                    <div class="col-md-12">

                                        <h3 class="settings-sub-header text-uppercase">{{ tr('social_settings') }}</h3>

                                        <hr>

                                    </div>

                                    <div class="col-md-6 col-sm-6">

                                        <div class="form-group">

                                            <label for="facebook_link">{{ tr('facebook_link') }} *</label>

                                            <input type="text" class="form-control" id="facebook_link" name="facebook_link" placeholder="Enter {{ tr('facebook_link') }}" value="{{ old('facebook_link') ? old('facebook_link') : Setting::get('facebook_link') }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="twitter_link">{{ tr('twitter_link') }} *</label>

                                            <input type="text" class="form-control" id="twitter_link" name="twitter_link" placeholder="Enter {{ tr('twitter_link') }}" value="{{ old('twitter_link') ? old('twitter_link') : Setting::get('twitter_link') }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="linkedin_link">{{ tr('linkedin_link') }} *</label>

                                            <input type="text" class="form-control" id="linkedin_link" name="linkedin_link" placeholder="Enter {{ tr('linkedin_link') }}" value="{{ old('linkedin_link') ? old('linkedin_link') : Setting::get('linkedin_link') }}">
                                        </div>                                

                                    </div>

                                    <div class="col-md-6 col-sm-6">

                                        <div class="form-group">
                                            <label for="google_plus_link">{{ tr('google_plus_link') }} *</label>

                                            <input type="text" class="form-control" id="google_plus_link" name="google_plus_link" placeholder="Enter {{ tr('google_plus_link') }}" value="{{ old('google_plus_link') ? old('google_plus_link') : Setting::get('google_plus_link') }}">
                                        </div>    

                                        
                                        <div class="form-group">
                                            <label for="pinterest_link">{{ tr('pinterest_link') }} *</label>
                                            
                                            <input type="text" class="form-control" id="pinterest_link" name="pinterest_link" placeholder="Enter {{ tr('pinterest_link') }}" value="{{ old('pinterest_link') ? old('pinterest_link') : Setting::get('pinterest_link') }}">
                                        </div>
                                        
                                    </div>


                                    <div class="col-md-12">

                                        <h3 class="settings-sub-header text-uppercase">{{ tr('apps_settings') }}</h3>

                                        <hr>

                                    </div>

                                    <div class="col-md-6 col-sm-6">

                                        <div class="form-group">
                                            <label for="playstore_user">{{ tr('playstore_user') }} *</label>
                                            <input type="text" class="form-control" id="playstore_user" name="playstore_user" placeholder="Enter {{ tr('playstore_user') }}" value="{{ old('playstore_user') ? old('playstore_user') : Setting::get('playstore_user') }}">
                                        </div>

                                    </div>

                                    <div class="col-md-6 col-sm-6">

                                        <div class="form-group">
                                            <label for="appstore_user">{{ tr('appstore_user') }} *</label>

                                            <input type="text" class="form-control" id="appstore_user" name="appstore_user" placeholder="Enter {{ tr('appstore_user') }}" value="{{ old('appstore_user') ? old('appstore_user') : Setting::get('appstore_user') }}">
                                        </div>                                        

                                    </div>

                                </div>

                            </div>

                            <div class="form-actions">

                                <div class="pull-right">

                                    <button type="reset"  class="btn btn-warning mr-1 ">
                                        <i class="ft-x "></i> {{ tr('reset') }}
                                    </button>
                                   
                                    <button type="submit" class="btn btn-primary" @if(Setting::get('is_demo_control_enabled') == YES) disabled  @endif ><i class="fa fa-check-square-o"></i>{{ tr('submit') }}</button>

                                </div>

                            </div>
                                
                            <div class="clearfix"></div>

                        </form>
                        <!-- <p>Sugar plum tootsica.</p> -->
                    
                    </div>

                    <!-- social_and_app_settings settings End -->

                    <!-- Other Settings START -->

                    <div class="tab-pane" id="other_settings_tab" aria-labelledby="other_settings">

                        <form action="{{ (Setting::get('is_demo_control_enabled') == YES) ? '#' : route('admin.settings.save') }}" method="POST" enctype="multipart/form-data" role="form">

                        @csrf
                            
                            <div class="form-body">

                                <div class="col-md-12 col-sm-12">

                                    <div class="form-group">
                                        <label for="google_analytics">{{ tr('google_analytics') }} *</label>

                                        <textarea class="form-control" name="google_analytics" placeholder="Enter {{ tr('google_analytics') }}">{{ old('google_analytics') ? old('google_analytics') : Setting::get('google_analytics') }}</textarea>

                                    </div>

                                    <div class="form-group">
                                        <label for="header_scripts">{{ tr('header_scripts') }} *</label>

                                        <textarea class="form-control" name="header_scripts" placeholder="Enter {{ tr('header_scripts') }}">{{ old('header_scripts') ? old('header_scripts') : Setting::get('header_scripts') }}</textarea>

                                    </div>

                                    <div class="form-group">
                                        <label for="body_scripts">{{ tr('body_scripts') }} *</label>

                                        <textarea class="form-control" name="body_scripts" placeholder="Enter {{ tr('body_scripts') }}">{{ old('body_scripts') ? old('body_scripts') : Setting::get('body_scripts') }}</textarea>

                                    </div>

                                </div>


                            </div>

                            <div class="form-actions">

                                <div class="pull-right">

                                    <button type="reset"  class="btn btn-warning mr-1 ">
                                        <i class="ft-x "></i> {{ tr('reset') }}
                                    </button>
                                   
                                    <button type="submit" class="btn btn-primary" @if(Setting::get('is_demo_control_enabled') == YES) disabled  @endif ><i class="fa fa-check-square-o"></i>{{ tr('submit') }}</button>
                                </div>

                            </div>
                            
                            <div class="clearfix"></div>

                        </form>
                        <!-- <p>Sugar plum tootsica.</p> -->
                    
                    </div>

                    <!-- Other Settings END -->

                </div>

            </div>

        </div>

    </div>

</div>

@endsection 

@section('scripts')

<script type="text/javascript">
</script>

@endsection