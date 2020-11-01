@extends('layouts.admin')

@section('title', tr('users'))

@section('content-header', tr('users'))

@section('breadcrumb')

    
    <li class="breadcrumb-item"><a href="{{route('admin.users.index')}}">{{tr('users')}}</a>
    </li>
    <li class="breadcrumb-item active">{{tr('view_user')}}</a>
    </li>

@endsection

@section('content')

<div class="content-body">

    <div id="user-profile">

        <div class="row">
  
            <div class="col-xl-12 col-lg-12">

                <div class="card">

                    <div class="card-header border-bottom border-gray">

                        <h4 class="card-title">{{tr('view_users')}}</h4>

                    </div>

                    <div class="card-content">

                        <div class="col-12">

                            <div class="card profile-with-cover">

                                <div class="media profil-cover-details w-100">

                                    <div class="media-left pl-2 pt-2">

                                        <a  class="profile-image">
                                          <img src="{{ $user->picture}}" alt="{{ $user->name}}" class="img-thumbnail img-fluid img-border height-100"
                                          alt="Card image">
                                        </a>
                                        
                                    </div>

                                    <div class="media-body pt-3 px-2">

                                        <div class="row">

                                            <div class="col">
                                                <h3 class="card-title">{{ $user->name }}</h3>
                                                <span class="text-muted">{{ $user->email }}</span>
                                            </div>

                                        </div>

                                    </div>
                                    
                                </div>

                                <nav class="navbar navbar-light navbar-profile align-self-end">
                                   
                                </nav>
                            </div>
                        </div>

                        <div class="table-responsive">

                            <table class="table table-xl mb-0">
                                <tr>
                                    <th>{{tr('username')}}</th>
                                    <td>{{$user->name}}</td>
                                </tr>

                                <tr>
                                    <th>{{tr('email')}}</th>
                                    <td>{{$user->email}}</td>
                                </tr> 

                                <tr>
                                    <th>{{tr('payment_mode')}}</th>
                                    <td>{{$user->payment_mode}}</td>
                                </tr>
                                
                                <tr>
                                    <th>{{tr('login_type')}}</th>
                                    <td>{{$user->login_by}}</td>
                                </tr>

                                <tr>
                                    <th>{{tr('device_type')}}</th>
                                    <td>{{$user->device_type}}</td>
                                </tr>

                                <tr>
                                    <th>{{tr('status')}}</th>
                                    <td>
                                        @if($user->status == USER_APPROVED) 

                                            <span class="badge badge-success">{{tr('approved')}}</span>

                                        @else
                                            <span class="badge badge-danger">{{tr('declined')}}</span>

                                        @endif
                                    </td>
                                </tr>

                                 <tr>
                                    <th>{{tr('gender')}}</th>
                                    <td>{{ucfirst($user->gender)}}</td>
                                </tr>

                                <tr>
                                    <th>{{tr('website')}}</th>
                                    <td><a href="{{$user->website}}" target="_blank">{{$user->website}}</a></td>
                                </tr>

                                <tr>
                                    <th>{{tr('amazon_wishlist')}}</th>
                                    <td><a href="{{$user->amazon_wishlist}}" target="_blank"> {{$user->amazon_wishlist}}</a></td>
                                </tr>


                                <tr>
                                    <th>{{tr('account_type')}}</th>
                                    <td>
                                        @if($user->user_account_type == USER_PREMIUM_ACCOUNT) 

                                            <span class="badge badge-success">{{tr('premium_users')}}</span>

                                        @else
                                            <span class="badge badge-danger">{{tr('free_users')}}</span>

                                        @endif
                                    </td>
                                </tr>

                                @if($user->user_account_type == USER_PREMIUM_ACCOUNT) 
                                <tr>
                                    <th>{{tr('monthly_amount')}}</th>
                                    <td>
                                        {{($user->userSubscription) ? formatted_amount($user->userSubscription->monthly_amount) : '-'}}
                                    </td>
                                </tr>

                                <tr>
                                    <th>{{tr('yearly_amount')}}</th>
                                    <td>
                                        {{($user->userSubscription) ? formatted_amount($user->userSubscription->yearly_amount) : '-'}}
                                    </td>
                                </tr>
                                @endif

                                <tr>
                                    <th>{{tr('mobile')}}</th>
                                    <td>{{$user->mobile}}</td>
                                </tr>

                                 <tr>
                                    <th>{{tr('user_wallet_balance')}}</th>
                                    <td>
                                       {{ ($user->userWallets) ? formatted_amount($user->userWallets->remaining) : '-' }}
                                    </td>
                                </tr>

                                <tr>
                                    <th>{{tr('email_notification')}}</th>
                                    <td>
                                        @if($user->is_email_notification == YES) 

                                            <span class="badge badge-success">{{tr('yes')}}</span>

                                        @else
                                            <span class="badge badge-danger">{{tr('no')}}</span>

                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th>{{tr('push_notification')}}</th>
                                    <td>
                                        @if($user->is_push_notification == YES) 

                                            <span class="badge badge-success">{{tr('yes')}}</span>

                                        @else
                                            <span class="badge badge-danger">{{tr('no')}}</span>

                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                  <th>{{tr('created_at')}} </th>
                                  <td>{{common_date($user->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                </tr>

                                <tr>
                                  <th>{{tr('updated_at')}} </th>
                                  <td>{{common_date($user->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
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

                                <a class="btn btn-outline-secondary btn-block btn-min-width mr-1 mb-1 " href="{{route('admin.users.edit', ['user_id'=>$user->id] )}}"> &nbsp;{{tr('edit')}}</a>

                            </div>

                            <div class="col-3">

                                <a class="btn btn-outline-danger btn-block btn-min-width mr-1 mb-1" onclick="return confirm(&quot;{{tr('user_delete_confirmation' , $user->name)}}&quot;);" href="{{route('admin.users.delete', ['user_id'=> $user->id] )}}">&nbsp;{{tr('delete')}}</a>

                            </div>

                            @endif

                            <div class="col-3">
                                
                                <a href="{{route('admin.delivery_address.index',['user_id' => $user->id])}}" class="btn btn-outline-warning btn-block btn-min-width mr-1 mb-1">{{tr('delivery_address')}}</a>

                            </div>

                            <div class="col-3">

                                @if($user->status == APPROVED)
                                     <a class="btn btn-outline-warning btn-block btn-min-width mr-1 mb-1" href="{{route('admin.users.status' ,['user_id'=> $user->id] )}}" onclick="return confirm(&quot;{{$user->name}} - {{tr('user_decline_confirmation')}}&quot;);">&nbsp;{{tr('decline')}} </a> 
                                @else

                                    <a  class="btn btn-outline-success btn-block btn-min-width mr-1 mb-1" href="{{route('admin.users.status' , ['user_id'=> $user->id] )}}">&nbsp;{{tr('approve')}}</a> 
                                @endif
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-3">
                                
                                <a href="{{route('admin.post.payments',['user_id' => $user->id])}}" class="btn btn-outline-warning btn-block btn-min-width mr-1 mb-1">{{tr('post_payments')}}</a>

                            </div>

                            <div class="col-3">
                                
                                <a href="{{route('admin.orders.index',['user_id' => $user->id])}}" class="btn btn-outline-success btn-block btn-min-width mr-1 mb-1">{{tr('orders')}}</a>

                            </div>


                              <div class="col-3">
                                
                                <a href="{{ route('admin.bookmarks.index', ['user_id' => $user->id] ) }}" class="btn btn-outline-warning btn-block btn-min-width mr-1 mb-1">{{tr('bookmarks')}}</a>

                            </div>

                            <div class="col-3">
                                
                                <a href="{{ route('admin.fav_users.index', ['user_id' => $user->id] ) }}" class="btn btn-outline-info btn-block btn-min-width mr-1 mb-1">{{tr('favorite_users')}}</a>

                            </div>

                              <div class="col-3">
                                
                                <a class="btn btn-outline-danger btn-block btn-min-width mr-1 mb-1" href="{{ route('admin.post_likes.index', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('liked_posts') }}</a>

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
