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

                                                <div class="card-header bg-card-header ">

                                                    <h4 class="">{{tr('admin_control')}}</h4>

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

        </div>

    </div>
    
</div>

@endsection 