@extends('layouts.admin') 

@section('title', tr('settings'))

@section('content-header', tr('settings'))

@section('breadcrumb')

<li class="breadcrumb-item active" aria-current="page">{{ tr('settings') }}</li>

@endsection 

@section('styles')

<style>
    
/*  fansclub tab */
div.fansclub-tab-container{
    z-index: 10;
    background-color: #ffffff;
    padding: 0 !important;
    border-radius: 4px;
    -moz-border-radius: 4px;
    border:1px solid #ddd;
    margin-top: 20px;
    margin-left: 50px;
    -webkit-box-shadow: 0 6px 12px rgba(3, 169, 243, 0.5);
    box-shadow: 0 6px 12px rgba(3, 169, 243, 0.5);
    -moz-box-shadow: 0 6px 12px rgba(3, 169, 243, 0.5);
    background-clip: padding-box;
    opacity: 0.97;
    filter: alpha(opacity=97);
}
div.fansclub-tab-menu{
    padding-right: 0;
    padding-left: 0;
    padding-bottom: 0;
}
div.fansclub-tab-menu div.list-group{
    margin-bottom: 0;
}
div.fansclub-tab-menu div.list-group>a{
    margin-bottom: 0;
}
div.fansclub-tab-menu div.list-group>a .glyphicon,
div.fansclub-tab-menu div.list-group>a .fa {
    color: #fea600;
}
div.fansclub-tab-menu div.list-group>a:first-child{
    border-top-right-radius: 0;
    -moz-border-top-right-radius: 0;
}
div.fansclub-tab-menu div.list-group>a:last-child{
    border-bottom-right-radius: 0;
    -moz-border-bottom-right-radius: 0;
}
div.fansclub-tab-menu div.list-group>a.active,
div.fansclub-tab-menu div.list-group>a.active .glyphicon,
div.fansclub-tab-menu div.list-group>a.active .fa{
    background-color: #fea600;
    background-image: #fea600;
    color: #ffffff;
    border: 2px dashed;
}
div.fansclub-tab-menu div.list-group>a.active:after{
    content: '';
    position: absolute;
    left: 100%;
    top: 50%;
    margin-top: -13px;
    border-left: 0;
    border-bottom: 13px solid transparent;
    border-top: 13px solid transparent;
    border-left: 10px solid #fea600;
}

div.fansclub-tab-content{
    background-color: #ffffff;
    /* border: 1px solid #eeeeee; */
    padding-left: 20px;
    padding-top: 10px;
}

.box-body {
    padding: 0px;
}

div.fansclub-tab div.fansclub-tab-content:not(.active){
  display: none;
}

.sub-title {
    width: fit-content;
    color: #2c648c;
    font-size: 18px;
    /*border-bottom: 2px dashed #285a86;*/
    padding-bottom: 5px;
}

hr {
    margin-top: 15px;
    margin-bottom: 15px;
}

.settings-sub-header {
    color: #f30660 !important;
}
</style>
@endsection

@section('content')

<div class="row">
    
     <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 fansclub-tab-menu">
        
        <div class="list-group">
            <a href="#" class="list-group-item active text-left text-uppercase">
                {{tr('site_settings')}}
            </a>
        
            <a href="#" class="list-group-item text-left text-uppercase">
                {{tr('payment_settings')}}
            </a>

            <a href="#" class="list-group-item text-left text-uppercase">
                {{tr('revenue_settings')}}
            </a>

            <a href="#" class="list-group-item text-left text-uppercase">
                {{tr('email_settings')}}
            </a>
            <a href="#" class="list-group-item text-left text-uppercase">
                {{tr('social_settings')}}
            </a>
            <a href="#" class="list-group-item text-left text-uppercase">
                {{tr('social_login')}}
            </a>
            <a href="#" class="list-group-item text-left text-uppercase">
                {{tr('notification_settings')}}
            </a>
            <a href="#" class="list-group-item text-left text-uppercase">
                {{tr('mobile_settings')}}
            </a>
            <a href="#" class="list-group-item text-left text-uppercase">
                {{tr('contact_information')}}
            </a>
            <a href="#" class="list-group-item text-left text-uppercase">
                {{tr('other_settings')}}
            </a>

        </div>

    </div>
    
    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 fansclub-tab">
        
        <!-- Site section -->            
        <div class="fansclub-tab-content active">

           <form id="site_settings_save" action="{{ route('admin.settings.save') }}" method="POST" enctype="multipart/form-data" role="form">

                @csrf

                <div class="box-body">

                    <div class="row">

                        <div class="col-md-12">

                            <h5 class="settings-sub-header text-uppercase"><b>{{tr('site_settings')}}</b></h5>
                            <hr>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group">
                                <label for="site_name">{{tr('site_name')}} *</label>
                                <input type="text" class="form-control" id="site_name" name="site_name" placeholder="Enter {{tr('site_name')}}" value="{{Setting::get('site_name')}}">
                            </div>

                            <div class="form-group">
                                <label for="tag_name">{{tr('tag_name')}} *</label>
                                <input type="text" class="form-control" id="tag_name" name="tag_name" placeholder="Enter {{tr('tag_name')}}" value="{{Setting::get('tag_name')}}">
                            </div>

                            <div class="form-group">
                                <label for="site_logo">{{tr('site_logo')}} *</label>
                                <p class="txt-warning">{{tr('png_image_note')}}</p>
                                <input type="file" class="form-control" id="site_logo" name="site_logo" accept="image/png" placeholder="{{tr('site_logo')}}">
                            </div>
                            
                            @if(Setting::get('site_logo'))

                                <img class="img img-thumbnail m-b-20" style="width: 40%" src="{{Setting::get('site_logo')}}" alt="{{Setting::get('site_name')}}"> 

                            @endif

                        </div>

                        <div class="col-lg-6">

                            <div class="form-group">

                                <label for="frontend_url">{{tr('frontend_url')}} *</label>

                                <input type="text" class="form-control" id="frontend_url" name="frontend_url" placeholder="{{tr('frontend_url')}}" value="{{Setting::get('frontend_url')}}">

                            </div>

                            <div class="form-group">

                                <label for="site_icon">{{tr('site_icon')}} *</label>

                                <p class="txt-warning">{{tr('png_image_note')}}</p>

                                <input type="file" class="form-control" id="site_icon" name="site_icon" accept="image/png" placeholder="{{tr('site_icon')}}">

                            </div>

                            @if(Setting::get('site_icon'))

                                <img class="img img-thumbnail m-b-20" style="width: 20%" src="{{Setting::get('site_icon')}}" alt="{{Setting::get('site_name')}}"> 

                            @endif

                        </div>

                    </div>

                </div>

                <!-- /.box-body -->

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

            <br>

        </div>

        <!-- Payment settings -->
        <div class="fansclub-tab-content">
            
            <form id="site_settings_save" action="{{route('admin.settings.save')}}" method="POST" enctype="multipart/form-data" class="forms-sample">
         
            @csrf

                <div class="box-body">

                    <div class="row">

                        <div class="col-md-12">

                            <h5 class="settings-sub-header text-uppercase" ><b>{{tr('payment_settings')}}</b></h5>

                            <hr>

                        </div>
                        <div class="col-md-12">

                            <h5 class="sub-title">{{tr('stripe_settings')}}</h5>

                        </div>

                         <div class="col-lg-6">
                             <div class="form-group">

                                <label for="stripe_publishable_key">{{tr('stripe_publishable_key')}} *</label>

                                <input type="text" class="form-control" id="stripe_publishable_key" name="stripe_publishable_key" placeholder="Enter {{tr('stripe_publishable_key')}}" value="{{old('stripe_publishable_key') ?: Setting::get('stripe_publishable_key')}}">

                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="stripe_secret_key">{{tr('stripe_secret_key')}} *</label>

                                <input type="text" class="form-control" id="stripe_secret_key" name="stripe_secret_key" placeholder="Enter {{tr('stripe_secret_key')}}" value="{{old('stripe_secret_key') ?: Setting::get('stripe_secret_key')}}">
                            </div>
                        </div>
                         <div class="col-lg-6">
                           <label for="stripe_secret_key">{{tr('stripe_mode')}} *</label>

                                <div class="clearfix"></div>

                                <div class="radio radio-aqua" style="display: inline-block;">

                                    <input id="stripe_live" name="stripe_mode" type="radio" value="{{ STRIPE_MODE_LIVE }}" @if(Setting::get('stripe_mode') == STRIPE_MODE_LIVE ) checked="checked" @endif>

                                    <label for="stripe_live">
                                        {{tr('live')}}
                                    </label>

                                </div>

                                <div class="radio radio-yellow" style="display: inline-block;">
                                    <input id="stripe_sandbox" name="stripe_mode" type="radio" value="{{ STRIPE_MODE_SANDBOX }}" @if(Setting::get( 'stripe_mode') == STRIPE_MODE_SANDBOX) checked="checked" @endif>
                                    <label for="stripe_sandbox">
                                        {{tr('sandbox')}}
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
       
            <br>
       
        </div>

          <!-- Revenue settings -->
        <div class="fansclub-tab-content">
            
            <form id="site_settings_save" action="{{route('admin.settings.save')}}" method="POST" enctype="multipart/form-data" class="forms-sample">
         
            @csrf

                <div class="box-body">

                    <div class="row">

                        <div class="col-md-12">

                            <h5 class="settings-sub-header text-uppercase" ><b>{{tr('revenue_settings')}}</b></h5>

                            <hr>

                        </div>
                        <div class="col-md-12">

                            <h5 class="sub-title">{{tr('stripe_settings')}}</h5>

                        </div>

                         <div class="col-lg-6">
                             <div class="form-group">

                                <label for="stripe_publishable_key">{{tr('stripe_publishable_key')}} *</label>

                                <input type="text" class="form-control" id="stripe_publishable_key" name="stripe_publishable_key" placeholder="Enter {{tr('stripe_publishable_key')}}" value="{{old('stripe_publishable_key') ?: Setting::get('stripe_publishable_key')}}">

                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="stripe_secret_key">{{tr('stripe_secret_key')}} *</label>

                                <input type="text" class="form-control" id="stripe_secret_key" name="stripe_secret_key" placeholder="Enter {{tr('stripe_secret_key')}}" value="{{old('stripe_secret_key') ?: Setting::get('stripe_secret_key')}}">
                            </div>
                        </div>
                         <div class="col-lg-6">
                           <label for="stripe_secret_key">{{tr('stripe_mode')}} *</label>

                                <div class="clearfix"></div>

                                <div class="radio radio-aqua" style="display: inline-block;">

                                    <input id="stripe_live" name="stripe_mode" type="radio" value="{{ STRIPE_MODE_LIVE }}" @if(Setting::get('stripe_mode') == STRIPE_MODE_LIVE ) checked="checked" @endif>

                                    <label for="stripe_live">
                                        {{tr('live')}}
                                    </label>

                                </div>

                                <div class="radio radio-yellow" style="display: inline-block;">
                                    <input id="stripe_sandbox" name="stripe_mode" type="radio" value="{{ STRIPE_MODE_SANDBOX }}" @if(Setting::get( 'stripe_mode') == STRIPE_MODE_SANDBOX) checked="checked" @endif>
                                    <label for="stripe_sandbox">
                                        {{tr('sandbox')}}
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
       
            <br>
       
        </div>

        <!-- Email settings -->
        <div class="fansclub-tab-content">
            <form id="site_settings_save" action="{{route('admin.env-settings.save')}}" method="POST">

            @csrf
        
                <div class="box-body">

                    <div class="row">

                        <div class="col-md-12">

                            <h5 class="settings-sub-header text-uppercase"><b>{{tr('email_settings')}}</b></h5>

                            <hr>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group">
                                    <label for="MAIL_MAILER">{{tr('MAIL_MAILER')}} *</label>
                                    <p class="text-muted">{{tr('MAIL_MAILER_note')}}</p>
                                    <input type="text" class="form-control" id="MAIL_MAILER" name="MAIL_MAILER" placeholder="Enter {{tr('MAIL_MAILER')}}" value="{{old('MAIL_MAILER') ?: $env_values['MAIL_MAILER'] }}">
                            </div>

                            <div class="form-group">
                                <label for="MAIL_HOST">{{tr('MAIL_HOST')}} *</label>
                                <p class="text-muted">{{tr('mail_host_note')}}</p>

                                <input type="text" class="form-control" id="MAIL_HOST" name="MAIL_HOST" placeholder="Enter {{tr('MAIL_HOST')}}" value="{{old('MAIL_HOST') ?: $env_values['MAIL_HOST']}}">
                            </div>

                            <div class="form-group">
                                <label for="MAIL_FROM_ADDRESS">{{tr('MAIL_FROM_ADDRESS')}} *</label>

                                <p class="text-muted">{{tr('MAIL_FROM_ADDRESS_note')}}</p>

                                <input type="text" class="form-control" id="MAIL_FROM_ADDRESS" name="MAIL_FROM_ADDRESS" placeholder="Enter {{tr('MAIL_FROM_ADDRESS')}}" value="{{old('MAIL_FROM_ADDRESS') ?: $env_values['MAIL_FROM_ADDRESS']}}">
                            </div>

                            <div class="form-group">
                                <label for="MAIL_PORT">{{tr('MAIL_PORT')}} *</label>

                                <p class="text-muted">{{tr('mail_port_note')}}</p>

                                <input type="text" class="form-control" id="MAIL_PORT" name="MAIL_PORT" placeholder="Enter {{tr('MAIL_PORT')}}" value="{{old('MAIL_PORT') ?: $env_values['MAIL_PORT']}}">
                            </div>
                        </div>

                        <div class="col-md-6">

                            <div class="form-group">
                                <label for="MAIL_USERNAME">{{tr('MAIL_USERNAME')}} *</label>

                                <p class="text-muted">{{tr('mail_username_note')}}</p>

                                <input type="text" class="form-control" id="MAIL_USERNAME" name="MAIL_USERNAME" placeholder="Enter {{tr('MAIL_USERNAME')}}" value="{{old('MAIL_USERNAME') ?: $env_values['MAIL_USERNAME']}}">
                            </div>

                            <div class="form-group">

                                <label for="MAIL_PASSWORD">{{tr('MAIL_PASSWORD')}} *</label>

                                <p class="text-muted" style="visibility: hidden;">{{tr('mail_username_note')}}</p>

                                <input type="password" class="form-control" id="MAIL_PASSWORD" name="MAIL_PASSWORD" placeholder="Enter {{tr('MAIL_PASSWORD')}}">
                            </div>

                            <div class="form-group">
                                <label for="MAIL_FROM_NAME">{{tr('MAIL_FROM_NAME')}} *</label>

                                <p class="text-muted">{{tr('MAIL_FROM_NAME_note')}}</p>

                                <input type="text" class="form-control" id="MAIL_FROM_NAME" name="MAIL_FROM_NAME" placeholder="Enter {{tr('MAIL_FROM_NAME')}}" value="{{old('MAIL_FROM_NAME') ?: $env_values['MAIL_FROM_NAME']}}">
                            </div>

                            <div class="form-group">
                                <label for="MAIL_ENCRYPTION">{{tr('MAIL_ENCRYPTION')}} *</label>

                                <p class="text-muted">{{tr('mail_encryption_note')}}</p>

                                <input type="text" class="form-control" id="MAIL_ENCRYPTION" name="MAIL_ENCRYPTION" placeholder="Enter {{tr('MAIL_ENCRYPTION')}}" value="{{old('MAIL_ENCRYPTION') ?: $env_values['MAIL_ENCRYPTION']}}">
                            </div>
                        </div>

                        <div class="clearfix"></div>

                        @if(isset($env_values['MAIL_MAILER']) && $env_values['MAIL_MAILER'] == 'mailgun')

                            <div class="col-md-12">

                                <div class="form-group">
                                    <label for="MAILGUN_DOMAIN">{{ tr('MAILGUN_DOMAIN') }}</label>
                                    <input type="text" class="form-control" value="{{ old('MAILGUN_DOMAIN') ?: $env_values['MAILGUN_DOMAIN']  }}" name="MAILGUN_DOMAIN" id="MAILGUN_DOMAIN" placeholder="{{ tr('MAILGUN_DOMAIN') }}">
                                </div>

                                <div class="form-group">
                                    <label for="MAILGUN_SECRET">{{ tr('MAILGUN_SECRET') }}</label>
                                    <input type="text" class="form-control" name="MAILGUN_SECRET" id="MAILGUN_SECRET" placeholder="{{ tr('MAILGUN_SECRET') }}" value="{{old('MAILGUN_SECRET') ?: $env_values['MAILGUN_SECRET'] }}">
                                </div>

                            </div>

                        @endif
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
       
            <br>
       
        </div>          

        <!-- Social Settings  -->
        <div class="fansclub-tab-content">
           
           <form id="site_settings_save" action="{{route('admin.settings.save')}}" method="POST">
                
                @csrf

                <div class="box-body">
                    <div class="row">

                        <div class="col-md-12">

                            <h5 class="settings-sub-header text-uppercase" ><b>{{tr('social_settings')}}</b></h5>

                            <hr>

                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="facebook_link">{{tr('facebook_link')}} *</label>

                                <input type="text" class="form-control" id="facebook_link" name="facebook_link" placeholder="Enter {{tr('facebook_link')}}" value="{{old('facebook_link') ?: Setting::get('facebook_link')}}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="linkedin_link">{{tr('linkedin_link')}} *</label>

                                <input type="text" class="form-control" id="linkedin_link" name="linkedin_link" placeholder="Enter {{tr('linkedin_link')}}" value="{{old('linkedin_link') ?: Setting::get('linkedin_link')}}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                    <label for="twitter_link">{{tr('twitter_link')}} *</label>

                                    <input type="text" class="form-control" id="twitter_link" name="twitter_link" placeholder="Enter {{tr('twitter_link')}}" value="{{old('twitter_link') ?: Setting::get('twitter_link')}}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pinterest_link">{{tr('pinterest_link')}} *</label>

                                <input type="text" class="form-control" id="pinterest_link" name="pinterest_link" placeholder="Enter {{tr('pinterest_link')}}" value="{{old('pinterest_link') ?: Setting::get('pinterest_link')}}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="instagram_link">{{tr('instagram_link')}} *</label>

                                <input type="text" class="form-control" id="instagram_link" name="instagram_link" placeholder="Enter {{tr('instagram_link')}}" value="{{old('instagram_link') ?: Setting::get('instagram_link')}}">
                            </div>
                        </div>
                        
                        <div class="clearfix"></div>
                        
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
        
            <br>
        
        </div>

        <!--Social login-->
        <div class="fansclub-tab-content">
           
           <form id="social_settings_save" action="{{route('admin.settings.save')}}" method="POST">
                
                @csrf

                <div class="box-body">

                    <div class="row">

                         <div class="col-md-12">

                           <h5 class="settings-sub-header text-uppercase" ><b>{{tr('social_login')}}</b></h5>

                            <hr>

                        </div>

                        <div class="col-md-12">
                            <h5 class="settings-sub-header text-uppercase text-danger"><b>{{tr('fb_settings')}}</b></h5>
                            <hr>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="FB_CLIENT_ID">{{tr('FB_CLIENT_ID')}} *</label>

                                <input type="text" class="form-control" name="FB_CLIENT_ID" id="FB_CLIENT_ID" placeholder="Enter {{tr('FB_CLIENT_ID')}}" value="{{old('FB_CLIENT_ID') ?: Setting::get('FB_CLIENT_ID') }}">
                            </div>
                        </div>
                       
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="FB_CLIENT_SECRET">{{tr('FB_CLIENT_SECRET')}} *</label>

                                <input type="text" class="form-control" name="FB_CLIENT_SECRET" id="FB_CLIENT_SECRET" placeholder="Enter {{tr('FB_CLIENT_SECRET')}}" value="{{old('FB_CLIENT_SECRET') ?: Setting::get('FB_CLIENT_SECRET') }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="FB_CALL_BACK">{{tr('FB_CALL_BACK')}} *</label>

                                <input type="text" class="form-control" name="FB_CALL_BACK" id="FB_CALL_BACK" placeholder="Enter {{tr('FB_CALL_BACK')}}" value="{{old('FB_CALL_BACK') ?: Setting::get('FB_CALL_BACK') }}">
                            </div>
                        </div>

                        <div class="col-md-12">

                            <h5 class="settings-sub-header text-uppercase text-danger"><b>{{tr('google_settings')}}</b></h5>

                            <hr>

                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="GOOGLE_CLIENT_ID">{{tr('GOOGLE_CLIENT_ID')}} *</label>

                                <input type="text" class="form-control" name="GOOGLE_CLIENT_ID" id="GOOGLE_CLIENT_ID" placeholder="Enter {{tr('GOOGLE_CLIENT_ID')}}" value="{{old('GOOGLE_CLIENT_ID') ?: Setting::get('GOOGLE_CLIENT_ID') }}">
                            </div>
                        </div>
                       
                         <div class="col-md-6">
                            <div class="form-group">
                                <label for="GOOGLE_CLIENT_SECRET">{{tr('GOOGLE_CLIENT_SECRET')}} *</label>

                                <input type="text" class="form-control" name="GOOGLE_CLIENT_SECRET" id="GOOGLE_CLIENT_SECRET" placeholder="Enter {{tr('GOOGLE_CLIENT_SECRET')}}" value="{{old('GOOGLE_CLIENT_SECRET') ?: Setting::get('GOOGLE_CLIENT_SECRET') }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="GOOGLE_CALL_BACK">{{tr('GOOGLE_CALL_BACK')}} *</label>

                                <input type="text" class="form-control" name="GOOGLE_CALL_BACK" id="GOOGLE_CALL_BACK" placeholder="Enter {{tr('GOOGLE_CALL_BACK')}}" value="{{old('GOOGLE_CALL_BACK') ?: Setting::get('GOOGLE_CALL_BACK') }}">
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
        
            <br>
        
        </div>

        <!--Notification settings -->
        <div class="fansclub-tab-content">
           
           <form id="social_settings_save" action="{{route('admin.settings.save')}}" method="POST">
                
                @csrf
                
                <div class="box-body">
                
                    <div class="row">

                        <div class="col-md-12">
                            <h5 class="settings-sub-header text-uppercase"><b>{{tr('notification_settings')}}</b></h5>
                            <hr>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <label for="user_fcm_sender_id">{{ tr('user_fcm_sender_id') }}</label>

                                <input type="text" class="form-control" name="user_fcm_sender_id" id="user_fcm_sender_id"
                                value="{{ Setting::get('user_fcm_sender_id') }}" placeholder="{{ tr('user_fcm_sender_id') }}">
                            </div>
                        </div>  

                        <div class="col-md-6">
                            <div class="form-group">

                                <label for="user_fcm_server_key">{{ tr('user_fcm_server_key') }}</label>

                                <input type="text" class="form-control" name="user_fcm_server_key" id="user_fcm_server_key"
                                value="{{ Setting::get('user_fcm_server_key') }}" placeholder="{{ tr('user_fcm_server_key') }}">
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
            <br>

        </div>  

        <!-- APP Url Settings -->
        <div class="fansclub-tab-content">
            
            <form id="site_settings_save" action="{{route('admin.settings.save')}}" method="POST">
                
                @csrf
                
                <div class="box-body">
                        
                    <div class="row">

                        <div class="col-md-12">

                            <h5 class="settings-sub-header text-uppercase" ><b>{{tr('mobile_settings')}}</b></h5>

                            <hr>

                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="playstore_user">{{tr('playstore_user')}} *</label>
                                <input type="text" class="form-control" id="playstore_user" name="playstore_user" placeholder="Enter {{tr('playstore_user')}}" value="{{old('playstore_user') ?: Setting::get('playstore_user')}}">
                            </div>

                        </div>

                        <div class="col-md-6">
                             <div class="form-group">
                                <label for="appstore_user">{{tr('appstore_user')}} *</label>

                                <input type="text" class="form-control" id="appstore_user" name="appstore_user" placeholder="Enter {{tr('appstore_user')}}" value="{{old('appstore_user') ?: Setting::get('appstore_user')}}">
                            </div>
                        </div>                       
                        
                    </div>

                </div>

                <div class="box-footer">

                    <button type="reset" class="btn btn-warning">{{tr('reset')}}</button>

                    @if(Setting::get('admin_delete_control') == 1)
                        <button type="submit" class="btn btn-primary pull-right" disabled>{{tr('submit')}}</button>
                    @else
                        <button type="submit" class="btn btn-success pull-right">{{tr('submit')}}</button>
                    @endif
       
                </div>
       
            </form>
       
            <br>
       
        </div>

        <!-- Contact Information -->
        <div class="fansclub-tab-content">
            
            <form id="site_settings_save" action="{{route('admin.settings.save')}}" method="POST">
                
                @csrf
                
                <div class="box-body">
                        
                    <div class="row">

                        <div class="col-md-12">

                            <h5 class="settings-sub-header text-uppercase" ><b>{{tr('contact_information')}}</b></h5>

                            <hr>

                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="copyright_content">{{tr('copyright_content')}} *</label>
                                <input type="text" class="form-control" id="copyright_content" name="copyright_content" placeholder="Enter {{tr('copyright_content')}}" value="{{old('copyright_content') ?: Setting::get('copyright_content')}}">
                            </div>

                        </div>

                        <div class="col-md-6">
                             <div class="form-group">
                                <label for="contact_mobile">{{tr('contact_mobile')}} *</label>

                                <input type="text" class="form-control" id="contact_mobile" name="contact_mobile" placeholder="Enter {{tr('contact_mobile')}}" value="{{old('contact_mobile') ?: Setting::get('contact_mobile')}}">
                            </div>
                        </div>

                        <div class="col-md-6">

                           <div class="form-group">
                                <label for="contact_email">{{tr('contact_email')}} *</label>

                                <input type="text" class="form-control" id="contact_email" name="contact_email" placeholder="Enter {{tr('contact_email')}}" value="{{old('contact_email') ?: Setting::get('contact_email')}}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact_address">{{tr('contact_address')}} *</label>

                                <input type="text" class="form-control" id="contact_address" name="contact_address" placeholder="Enter {{tr('contact_address')}}" value="{{old('contact_address') ?: Setting::get('contact_address')}}">
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
       
            <br>
       
        </div>

        <!-- OTHER Settings -->

        <div class="fansclub-tab-content">
        
            <form id="site_settings_save" action="{{route('admin.settings.save')}}" method="POST">
                
                @csrf
                
                <div class="box-body"> 
                    <div class="row"> 

                        <div class="col-md-12">

                            <h5 class="settings-sub-header text-uppercase" ><b>{{tr('other_settings')}}</b></h5>

                            <hr>

                        </div>

                        <div class="col-lg-12">

                            <div class="form-group">
                                <label for="google_analytics">{{tr('google_analytics')}}</label>
                                <textarea class="form-control" id="google_analytics" name="google_analytics">{{Setting::get('google_analytics')}}</textarea>
                            </div>

                        </div> 

                        <div class="col-lg-12">

                            <div class="form-group">
                                <label for="header_scripts">{{tr('header_scripts')}}</label>
                                <textarea class="form-control" id="header_scripts" name="header_scripts">{{Setting::get('header_scripts')}}</textarea>
                            </div>

                        </div> 

                        <div class="col-lg-12">

                            <div class="form-group">
                                <label for="body_scripts">{{tr('body_scripts')}}</label>
                                <textarea class="form-control" id="body_scripts" name="body_scripts">{{Setting::get('body_scripts')}}</textarea>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- /.box-body -->

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
        
            <br>
        
        </div>

    </div>

</div>



@endsection


@section('scripts')

<script type="text/javascript">
    
    $(document).ready(function() {
        $("div.fansclub-tab-menu>div.list-group>a").click(function(e) {
            e.preventDefault();
            $(this).siblings('a.active').removeClass("active");
            $(this).addClass("active");
            var index = $(this).index();
            $("div.fansclub-tab>div.fansclub-tab-content").removeClass("active");
            $("div.fansclub-tab>div.fansclub-tab-content").eq(index).addClass("active");
        });
    });
</script>
@endsection