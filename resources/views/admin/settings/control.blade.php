@extends('layouts.admin') 

@section('title', tr('admin_demo_control_settings')) 

@section('content-header', tr('admin_demo_control_settings')) 

@section('breadcrumb_left')

    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ tr('home') }}</a>
    </li>

    <li class="breadcrumb-item active">{{ tr('admin_demo_control_settings') }}</a>
    </li>

@endsection 

@section('breadcrumb_right')

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

                                            <div class="card-text">

                                            </div>

                                            <form class="form-horizontal" action="{{ route('admin.admin_control.save')}}" method="POST" enctype="multipart/form-data" role="form">
                                               
                                                @csrf
                                              
                                                <div class="form-body">

                                                    <div class="row">

                    	                                <div class="col-md-6">
                    	                                	
                    		                                <div class="form-group">
                                                                <label><b> {{ tr('demo_control')}}</b></label>
                    		                                    <div>   
                    										      <input type="radio" name="is_demo_control_enabled" value="1"  @if(Setting::get('is_demo_control_enabled') == YES) checked  @endif > <label> {{ tr('enable') }}</label>
                    										    
                    										      <input type="radio" value="0" name="is_demo_control_enabled" @if(Setting::get('is_demo_control_enabled') == NO) checked  @endif > <label> {{ tr('disable') }}</label>
                    										    </div>                             
                    		                            	</div>


                                                            <div class="form-group">
                                                                <label><b> {{ tr('is_email_notification')}}</b></label>
                                                                <div>
                                                                  <input type="radio" name="is_email_notification" value="1"  @if(Setting::get('is_email_notification') == YES) checked  @endif > <label> {{ tr('enable') }}</label>
                                                                
                                                                  <input type="radio" value="0" name="is_email_notification" @if(Setting::get('is_email_notification') == NO) checked  @endif >
                                                                  <label> {{ tr('disable') }}</label>
                                                                </div>                                                      
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">


                                                            <div class="form-group">
                                                                <label><b>{{ tr('is_push_notification')}}</b></label>
                                                                <div>
                                                                     <input type="radio" name="is_push_notification" value="1"  @if(Setting::get('is_push_notification') == YES) checked  @endif ><label> {{ tr('enable') }}</label>
                                                                
                                                                  <input type="radio" value="0" name="is_push_notification" @if(Setting::get('is_push_notification') == NO) checked  @endif > <label>{{ tr('disable') }}</label>
                                                                </div>                                                      
                                                            </div>

                                                            <div class="form-group">
                                                                <label><b>{{ tr('is_account_email_verification')}}</b></label>
                                                                <div>
                                                                    <input type="radio" name="is_account_email_verification" value="1"  @if(Setting::get('is_account_email_verification') == YES) checked  @endif > <label> {{ tr('enable') }}</label>
                                                                
                                                                 <input type="radio" value="0" name="is_account_email_verification" @if(Setting::get('is_account_email_verification') == NO) checked  @endif > <label> {{ tr('disable') }}</label>
                                                                </div>                                                      
                                                            </div>

                                                        </div>

                                                        <div class="col-md-6">

                                                            <div class="form-group">
                                                                <label><b>{{ tr('appstore_update_status')}}</b></label>
                                                                <div>
                                                                     <input type="radio" name="appstore_update_status" value="1"  @if(Setting::get('appstore_update_status') == YES) checked  @endif ><label> {{ tr('yes') }}</label>
                                                                
                                                                  <input type="radio" value="0" name="appstore_update_status" @if(Setting::get('appstore_update_status') == NO) checked  @endif > <label>{{ tr('no') }}</label>
                                                                </div>                                                      
                                                            </div>


                    	                            	</div>

                                                	</div>
                                              
                    	                            <div class="form-actions">

                    	                                <div class="pull-right">
                    	                                
                    	                                        <button type="reset" class="btn btn-warning mr-1">
                    	                                            <i class="ft-x"></i> {{tr('reset')}} 
                    	                                        </button>

                    	                                        <button type="submit" class="btn btn-primary" ><i class="fa fa-check-square-o"></i>{{ tr('submit') }}</button>
                    	                                
                    	                                </div>

                    	                                <div class="clearfix"></div>

                    	                            </div>

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