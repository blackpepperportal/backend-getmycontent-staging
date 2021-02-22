@extends('layouts.admin') 

@section('title', tr('admin_control')) 

@section('content-header', tr('admin_control')) 

@section('breadcrumb')

<li class="breadcrumb-item active">{{ tr('admin_control') }}</li>

@endsection 

@section('content')

<div id="user-profile">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-content">

                	<section id="basic-form-layouts">
    
                        <div class="row match-height">
                        
                            <div class="col-lg-12">

                                <div class="card">
                                    
                                    <div class="card-header border-bottom border-gray">
                                        <h4 class="card-title" id="basic-layout-form">{{tr('admin')}}</h4>
                                        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                                    </div>

                                    <div class="card-content collapse show">

                                        <div class="card-body">

                                            <form class="forms-sample" action="{{ route('admin.settings.save') }}" method="POST" enctype="multipart/form-data" role="form">

                                            @csrf

                                                <div class="card-header">

                                                    <h4 class="text-uppercase">{{tr('admin_control')}}</h4>

                                                    <hr>

                                                </div>

                                                <div class="card-body">

                                                    <div class="row">

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('is_demo_control_enabled') }}</label>
                                                            <br>
                                                            <label>
                                                                <input required type="radio" name="is_demo_control_enabled" value="1" class="flat-red" @if(Setting::get('is_demo_control_enabled') == 1) checked @endif>
                                                                {{tr('yes')}}
                                                            </label>

                                                            <label>
                                                                <input required type="radio" name="is_demo_control_enabled" class="flat-red"  value="0" @if(Setting::get('is_demo_control_enabled') == 0) checked @endif>
                                                                {{tr('no')}}
                                                            </label>
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('is_account_email_verification') }}</label>
                                                            <br>
                                                            <label>
                                                                <input required type="radio" name="is_account_email_verification" value="1" class="flat-red" @if(Setting::get('is_account_email_verification') == 1) checked @endif>
                                                                {{tr('yes')}}
                                                            </label>

                                                            <label>
                                                                <input required type="radio" name="is_account_email_verification" class="flat-red"  value="0" @if(Setting::get('is_account_email_verification') == 0) checked @endif>
                                                                {{tr('no')}}
                                                            </label>
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('is_mailgun_email_validate') }}</label>
                                                            <br>
                                                            <label>
                                                                <input required type="radio" name="is_mailgun_email_validate" value="1" class="flat-red" @if(Setting::get('is_mailgun_email_validate') == 1) checked @endif>
                                                                {{tr('yes')}}
                                                            </label>

                                                            <label>
                                                                <input required type="radio" name="is_mailgun_email_validate" class="flat-red"  value="0" @if(Setting::get('is_mailgun_email_validate') == 0) checked @endif>
                                                                {{tr('no')}}
                                                            </label>
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('is_email_notification') }}</label>
                                                            <br>
                                                            <label>
                                                                <input required type="radio" name="is_email_notification" value="1" class="flat-red" @if(Setting::get('is_email_notification') == 1) checked @endif>
                                                                {{tr('yes')}}
                                                            </label>

                                                            <label>
                                                                <input required type="radio" name="is_email_notification" class="flat-red"  value="0" @if(Setting::get('is_email_notification') == 0) checked @endif>
                                                                {{tr('no')}}
                                                            </label>
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('is_email_configured') }}</label>
                                                            <br>
                                                            <label>
                                                                <input required type="radio" name="is_email_configured" value="1" class="flat-red" @if(Setting::get('is_email_configured') == 1) checked @endif>
                                                                {{tr('yes')}}
                                                            </label>

                                                            <label>
                                                                <input required type="radio" name="is_email_configured" class="flat-red"  value="0" @if(Setting::get('is_email_configured') == 0) checked @endif>
                                                                {{tr('no')}}
                                                            </label>
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('is_push_notification') }}</label>
                                                            <br>
                                                            <label>
                                                                <input required type="radio" name="is_push_notification" value="1" class="flat-red" @if(Setting::get('is_push_notification') == 1) checked @endif>
                                                                {{tr('yes')}}
                                                            </label>

                                                            <label>
                                                                <input required type="radio" name="is_push_notification" class="flat-red"  value="0" @if(Setting::get('is_push_notification') == 0) checked @endif>
                                                                {{tr('no')}}
                                                            </label>
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('notification_count_update_enabled') }}</label>
                                                            <br>
                                                            <label>
                                                                <input required type="radio" name="is_notification_count_enabled" value="1" class="flat-red" @if(Setting::get('is_notification_count_enabled') == 1) checked @endif>
                                                                {{tr('yes')}}
                                                            </label>

                                                            <label>
                                                                <input required type="radio" name="is_notification_count_enabled" class="flat-red"  value="0" @if(Setting::get('is_notification_count_enabled') == 0) checked @endif>
                                                                {{tr('no')}}
                                                            </label>
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('s3_bucket') }}</label>
                                                            <br>
                                                            <label>
                                                                <input required type="radio" name="s3_bucket" value="1" class="flat-red" @if(Setting::get('s3_bucket') == 1) checked @endif>
                                                                {{tr('enable')}}
                                                            </label>

                                                            <label>
                                                                <input required type="radio" name="s3_bucket" class="flat-red"  value="0" @if(Setting::get('s3_bucket') == 0) checked @endif>
                                                                {{tr('disable')}}
                                                            </label>
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('is_verified_badge_enabled') }}</label>
                                                            <br>
                                                            <label>
                                                                <input required type="radio" name="is_verified_badge_enabled" value="1" class="flat-red" @if(Setting::get('is_verified_badge_enabled') == 1) checked @endif>
                                                                {{tr('enable')}}
                                                            </label>

                                                            <label>
                                                                <input required type="radio" name="is_verified_badge_enabled" class="flat-red"  value="0" @if(Setting::get('is_verified_badge_enabled') == 0) checked @endif>
                                                                {{tr('disable')}}
                                                            </label>
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('paypal_payment_status') }}</label>
                                                            <br>
                                                            <label>
                                                                <input required type="radio" name="is_paypal_enabled" value="1" class="flat-red" @if(Setting::get('is_paypal_enabled') == 1) checked @endif>
                                                                {{tr('enable')}}
                                                            </label>

                                                            <label>
                                                                <input required type="radio" name="is_paypal_enabled" class="flat-red"  value="0" @if(Setting::get('is_paypal_enabled') == 0) checked @endif>
                                                                {{tr('disable')}}
                                                            </label>
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('is_user_active_status') }}</label>
                                                            <br>
                                                            <label>
                                                                <input required type="radio" name="is_user_active_status" value="1" class="flat-red" @if(Setting::get('is_user_active_status') == 1) checked @endif>
                                                                {{tr('enable')}}
                                                            </label>

                                                            <label>
                                                                <input required type="radio" name="is_user_active_status" class="flat-red"  value="0" @if(Setting::get('is_user_active_status') == 0) checked @endif>
                                                                {{tr('disable')}}
                                                            </label>
                                                    
                                                        </div>

                                                        <div class="clearfix"></div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('admin_take_count') }}</label>
                                                            
                                                            <input type="number" name="admin_take_count" class="form-control" value="{{Setting::get('admin_take_count', 6)}}">
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('currency') }}</label>
                                                            
                                                            <input type="text" name="currency" class="form-control" value="{{Setting::get('currency', '$')}}">
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('currency_code') }}</label>
                                                            
                                                            <input type="text" name="currency_code" class="form-control" value="{{Setting::get('currency_code', 'USD')}}">
                                                    
                                                        </div>

                                                    </div>

                                                    <div class="clearfix"></div>

                                                    <div class="row">

                                                        <div class="col-md-12">

                                                            <hr><h4>Demo Login Details</h4><hr>

                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('demo_admin_email') }}</label>
                                                            
                                                            <input type="text" name="demo_admin_email" class="form-control" value="{{Setting::get('demo_admin_email')}}">
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('demo_admin_password') }}</label>
                                                            
                                                            <input type="text" name="demo_admin_password" class="form-control" value="{{Setting::get('demo_admin_password')}}">
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('demo_user_email') }}</label>
                                                            
                                                            <input type="text" name="demo_user_email" class="form-control" value="{{Setting::get('demo_user_email')}}">
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('demo_user_password') }}</label>
                                                            
                                                            <input type="text" name="demo_user_password" class="form-control" value="{{Setting::get('demo_user_password')}}">
                                                    
                                                        </div>
                                                    
                                                    </div>
                                                    <div class="row">

                                                        <div class="col-md-12">

                                                            <hr><h4>Frontend Settings</h4><hr>

                                                        </div>

                                                        <div class="col-md-6">

                                                            <div class="form-group">
                                                                <label for="frontend_no_data_image">{{tr('frontend_no_data_image')}} *</label>
                                                                <input type="file" class="form-control" id="frontend_no_data_image" name="frontend_no_data_image" accept="image/png" placeholder="{{tr('frontend_no_data_image')}}">
                                                            </div>
                                                            
                                                            @if(Setting::get('frontend_no_data_image'))

                                                                <img class="img img-thumbnail m-b-20" style="width: 40%" src="{{Setting::get('frontend_no_data_image')}}" alt="{{Setting::get('site_name')}}"> 

                                                            @endif

                                                        </div>
                                                    
                                                    </div>

                                                    <div class="row">

                                                        <div class="col-md-12">

                                                            <hr><h4>Push Notification Links</h4><hr>

                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>BN_USER_FOLLOWINGS</label>
                                                            
                                                            <input type="text" name="BN_USER_FOLLOWINGS" class="form-control" value="{{Setting::get('BN_USER_FOLLOWINGS')}}">
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('BN_USER_COMMENT') }}</label>
                                                            
                                                            <input type="text" name="BN_USER_COMMENT" class="form-control" value="{{Setting::get('BN_USER_COMMENT')}}">
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('BN_USER_LIKE') }}</label>
                                                            
                                                            <input type="text" name="BN_USER_LIKE" class="form-control" value="{{Setting::get('BN_USER_LIKE')}}">
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('BN_USER_TIPS') }}</label>
                                                            
                                                            <input type="text" name="BN_USER_TIPS" class="form-control" value="{{Setting::get('BN_USER_TIPS')}}">
                                                    
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

                </div>

            </div>

            <div class="card">

                <div class="card-content">

                    <section id="basic-form-layouts">
    
                        <div class="row match-height">
                        
                            <div class="col-lg-12">

                                <div class="card">
                                    
                                    <div class="card-header border-bottom border-gray">
                                        <h4 class="card-title" id="basic-layout-form">{{tr('admin')}}</h4>
                                        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                                    </div>

                                    <div class="card-content collapse show">

                                        <div class="card-body">


                                            <form class="forms-sample" action="{{route('admin.env-settings.save')}}" method="POST" role="form">

                                            @csrf

                                                <div class="card-header bg-card-header ">

                                                    <h4 class="">{{tr('s3_bucket_config')}}</h4>

                                                </div>

                                                <div class="card-body">

                                                    <div class="row">

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('AWS_ACCESS_KEY_ID') }}</label>
                                                            <br>
                                                            <input type="text" class="form-control" id="AWS_ACCESS_KEY_ID" name="AWS_ACCESS_KEY_ID" placeholder="Enter {{tr('AWS_ACCESS_KEY_ID')}}" value="{{old('AWS_ACCESS_KEY_ID') ?: $env_values['AWS_ACCESS_KEY_ID'] }}">
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('AWS_SECRET_ACCESS_KEY') }}</label>
                                                            <br>
                                                            <input type="text" class="form-control" id="AWS_SECRET_ACCESS_KEY" name="AWS_SECRET_ACCESS_KEY" placeholder="Enter {{tr('AWS_SECRET_ACCESS_KEY')}}" value="{{old('AWS_SECRET_ACCESS_KEY') ?: $env_values['AWS_SECRET_ACCESS_KEY'] }}">
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('AWS_DEFAULT_REGION') }}</label>
                                                            <br>
                                                            <input type="text" class="form-control" id="AWS_DEFAULT_REGION" name="AWS_DEFAULT_REGION" placeholder="Enter {{tr('AWS_DEFAULT_REGION')}}" value="{{old('AWS_DEFAULT_REGION') ?: $env_values['AWS_DEFAULT_REGION'] }}">
                                                    
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                                       
                                                            <label>{{ tr('AWS_BUCKET') }}</label>
                                                            <br>
                                                            <input type="text" class="form-control" id="AWS_BUCKET" name="AWS_BUCKET" placeholder="Enter {{tr('AWS_BUCKET')}}" value="{{old('AWS_BUCKET') ?: $env_values['AWS_BUCKET'] }}">
                                                    
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

                </div>

            </div>

        </div>

    </div>
    
</div>

@endsection 