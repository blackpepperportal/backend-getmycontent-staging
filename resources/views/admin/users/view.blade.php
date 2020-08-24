@extends('layouts.admin')

@section('title', tr('view_users'))

@section('content-header', tr('view_users'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{tr('home')}}</a>
    </li>
    <li class="breadcrumb-item"><a href="{{route('admin.users.index')}}">{{tr('users')}}</a>
    </li>
    <li class="breadcrumb-item active">{{tr('view_users')}}</a>
    </li>

@endsection

@section('content')

<div class="content-body">

    <div id="user-profile">

        <div class="row">

            <div class="col-12">

                <div class="card profile-with-cover">

                    <div class="media profil-cover-details w-100">

                        <div class="media-left pl-2 pt-2">

                            <a  class="profile-image">
                              <img src="{{ $user_details->picture}}" alt="{{ $user_details->name}}" class="img-thumbnail img-fluid img-border height-100"
                              alt="Card image">
                            </a>
                            
                        </div>

                        <div class="media-body pt-3 px-2">

                            <div class="row">

                                <div class="col">
                                    <h3 class="card-title">{{ $user_details->name }}</h3>
                                    <span class="text-muted">{{ $user_details->email }}</span>
                                </div>

                            </div>

                        </div>
                        
                    </div>

                    <nav class="navbar navbar-light navbar-profile align-self-end">
                       
                    </nav>
                </div>
            </div>
  
            <div class="col-xl-12 col-lg-12">

                <div class="card">

                    <div class="card-header border-bottom border-gray">

                          <h4 class="card-title">{{tr('user_details')}}</h4>
                    </div>

                    <div class="card-content">

                        <div class="table-responsive">

                            <table class="table table-xl mb-0">
                                <tr>
                                    <th>{{tr('username')}}</th>
                                    <td>{{$user_details->name}}</td>
                                </tr>

                                <tr>
                                    <th>{{tr('email')}}</th>
                                    <td>{{$user_details->email}}</td>
                                </tr> 

                                <tr>
                                    <th>{{tr('payment_mode')}}</th>
                                    <td>{{$user_details->payment_mode}}</td>
                                </tr>
                                
                                <tr>
                                    <th>{{tr('login_type')}}</th>
                                    <td>{{$user_details->login_by}}</td>
                                </tr>

                                <tr>
                                    <th>{{tr('device_type')}}</th>
                                    <td>{{$user_details->device_type}}</td>
                                </tr>

                                <tr>
                                    <th>{{tr('status')}}</th>
                                    <td>
                                        @if($user_details->status == USER_APPROVED) 

                                            <span class="badge badge-success">{{tr('approved')}}</span>

                                        @else
                                            <span class="badge badge-danger">{{tr('declined')}}</span>

                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th>{{tr('email_notification')}}</th>
                                    <td>
                                        @if($user_details->email_notification_status == YES) 

                                            <span class="badge badge-success">{{tr('yes')}}</span>

                                        @else
                                            <span class="badge badge-danger">{{tr('no')}}</span>

                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th>{{tr('push_notification')}}</th>
                                    <td>
                                        @if($user_details->push_notification_status == YES) 

                                            <span class="badge badge-success">{{tr('yes')}}</span>

                                        @else
                                            <span class="badge badge-danger">{{tr('no')}}</span>

                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                  <th>{{tr('created_at')}} </th>
                                  <td>{{common_date($user_details->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                </tr>

                                <tr>
                                  <th>{{tr('updated_at')}} </th>
                                  <td>{{common_date($user_details->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
                                </tr>
                                
                            </table>

                        </div>

                    </div>

                    <div class="card-footer">

                        <div class="card-title">
                            {{tr('action')}}
                        </div>

                        <div class="row">

                            @if(Setting::get('is_demo_control_enabled') == YES)

                            <div class="col-3">

                                <a class="btn btn-outline-secondary btn-block btn-min-width mr-1 mb-1 " href="javascript:void(0)"> &nbsp;{{tr('edit')}}</a>

                            </div>

                            <div class="col-3">

                                <a class="btn btn-outline-danger btn-block btn-min-width mr-1 mb-1" href="javascript:void(0)">&nbsp;{{tr('delete')}}</a>

                            </div>


                            @else

                            <div class="col-3">

                                <a class="btn btn-outline-secondary btn-block btn-min-width mr-1 mb-1 " href="{{route('admin.users.edit', ['user_id'=>$user_details->id] )}}"> &nbsp;{{tr('edit')}}</a>

                            </div>

                            <div class="col-3">

                                <a class="btn btn-outline-danger btn-block btn-min-width mr-1 mb-1" onclick="return confirm(&quot;{{tr('admin_user_delete_confirmation' , $user_details->name)}}&quot;);" href="{{route('admin.users.delete', ['user_id'=> $user_details->id] )}}">&nbsp;{{tr('delete')}}</a>

                            </div>

                            @endif

                            <div class="col-3">
                                
                                <a href="{{route('admin.orders.index',['user_id' => $user_details->id])}}" class="btn btn-outline-warning btn-block btn-min-width mr-1 mb-1">{{tr('orders')}}</a>

                            </div>

                            <div class="col-3">
                                
                                <a href="{{route('admin.delivery_address.index',['user_id' => $user_details->id])}}" class="btn btn-outline-warning btn-block btn-min-width mr-1 mb-1">{{tr('delivery_address')}}</a>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-3">

                                @if($user_details->status == APPROVED)
                                     <a class="btn btn-outline-warning btn-block btn-min-width mr-1 mb-1" href="{{route('admin.users.status' ,['user_id'=> $user_details->id] )}}" onclick="return confirm(&quot;{{$user_details->name}} - {{tr('user_decline_confirmation')}}&quot;);">&nbsp;{{tr('decline')}} </a> 
                                @else

                                    <a  class="btn btn-outline-success btn-block btn-min-width mr-1 mb-1" href="{{route('admin.users.status' , ['user_id'=> $user_details->id] )}}">&nbsp;{{tr('approve')}}</a> 
                                @endif
                            </div>

                            <div class="col-3">
                                
                                <a href="{{route('admin.post.payments',['user_id' => $user_details->id])}}" class="btn btn-outline-warning btn-block btn-min-width mr-1 mb-1">{{tr('post_payments')}}</a>

                            </div>

                            <div class="col-3">
                                
                                <a href="{{route('admin.order.payments',['user_id' => $user_details->id])}}" class="btn btn-outline-success btn-block btn-min-width mr-1 mb-1">{{tr('order_payments')}}</a>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
  
    
@endsection

@section('scripts')

@endsection
