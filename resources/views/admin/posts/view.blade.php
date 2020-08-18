@extends('layouts.admin')

@section('title', tr('posts'))

@section('content-header', tr('posts'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{tr('home')}}</a>
    </li>

    <li class="breadcrumb-item active">{{tr('posts')}}</a>
    </li>

@endsection

@section('content')

<div class="content-body">

    <div class="col-12">

        <div class="card">

            <div class="card-header border-bottom border-gray">

                <h4 class="card-title">{{ tr('posts') }}</h4>
                <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-1">

                        <img src="{{$post_details->getStardomDetails->picture ?? asset('placeholder.jpg')}}" class="height-100" alt="Card image" />

                    </div>

                    <div class="col-4">
                        
                        <h4 class="card-title">{{$post_details->getStardomDetails->name ?? "-"}}</h4>

                        <h6 class="card-subtitle text-muted">{{$post_details->getStardomDetails->email ?? "-"}}</h6>
                        <br>

                        <a href="{{route('admin.stardoms.view',['stardom_id' => $post_details->stardom_id])}}" class="btn btn-primary">
                            {{tr('go_to_profile')}}
                        </a>

                        <a href="{{route('admin.post.payments',['post_id'=>$post_details->id])}}" class="btn btn-purple">{{tr('payments')}}</a>

                    </div>

                </div>
                <hr>

                <div class="row">

                    <div class="col-xl-4 col-lg-6 col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="media align-items-stretch">
                                    <div class="p-2 text-center bg-success bg-darken-2">
                                        <i class="icon-trophy font-large-2 white"></i>
                                    </div>
                                    <div class="p-2 bg-gradient-x-success white media-body">
                                        <h5>{{tr('total_earnings')}}</h5>
                                        <h5 class="text-bold-400 mb-0">{{$payment_data->total_earnings}}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-6 col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="media align-items-stretch">
                                    <div class="p-2 text-center bg-danger bg-darken-2">
                                        <i class="icon-present font-large-2 white"></i>
                                    </div>
                                    <div class="p-2 bg-gradient-x-danger white media-body">
                                        <h5>{{tr('current_month_earnings')}}</h5>
                                        <h5 class="text-bold-400 mb-0">{{$payment_data->current_month_earnings}}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-6 col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="media align-items-stretch">
                                    <div class="p-2 text-center bg-warning bg-darken-2">
                                        <i class="icon-diamond font-large-2 white"></i>
                                    </div>
                                    <div class="p-2 bg-gradient-x-warning white media-body">
                                        <h5>{{tr('today_earnings')}}</h5>
                                        <h5 class="text-bold-400 mb-0">{{$payment_data->today_earnings}}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-12">

                        <h3 class="text-uppercase">{{tr('post_details')}}</h3>

                        @if(Setting::get('is_demo_control_enabled') == YES)

                            <a class="btn-sm btn-danger" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a> 

                        @else

                            <a class="btn-sm btn-danger" onclick="return confirm(&quot;{{ tr('post_delete_confirmation' , $post_details->name) }}&quot;);" href="{{ route('admin.posts.delete', ['post_id' => $post_details->id] ) }}">&nbsp;{{ tr('delete') }}</a>

                        @endif

                        @if($post_details->status == APPROVED)

                            <a class="btn-sm btn-secondary" href="{{  route('admin.posts.status' , ['post_id' => $post_details->id] )  }}" onclick="return confirm(&quot;{{ tr('post_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                            </a> 

                        @else

                            <a class="btn btn-success" href="{{ route('admin.posts.status' , ['post_id' => $post_details->id] ) }}">&nbsp;{{ tr('approve') }}</a> 

                        @endif
                        <hr>
                    </div>
                   
                    <div class="col-6">
                        
                        <ul>
                            <li class="text-uppercase">{{tr('unique_id')}} - {{$post_details->unique_id}}</li>
                            <hr>

                            <li>{{tr('publish_time')}} - {{common_date($post_details->publish_time , Auth::guard('admin')->user()->timezone)}}</li>
                            <hr>

                            <li>{{tr('is_paid_post')}} -                    @if($post_details->is_paid_post)
                                <span class="badge badge-success">{{tr('yes')}}</span>
                            @else
                                <span class="badge badge-danger">{{tr('no')}}</span>
                            @endif
                            </li>
                            <hr>

                            <li>{{tr('amount')}}- {{$post_details->amount_formatted}}</li>
                            <hr>

                        </ul>
                    </div>

                    <div class="col-6">

                        <ul>
                            <li>{{tr('content')}}-{{$post_details->content}}</li>
                            <hr>

                            <li>{{tr('status')}} - 

                                @if($post_details->status == APPROVED)

                                    <span class="btn btn-success btn-sm">{{ tr('approved') }}</span> 
                                @else

                                    <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span> 
                                @endif
                            </li>
                            <hr>

                            <li>{{tr('created_at')}} - {{common_date($post_details->created_at , Auth::guard('admin')->user()->timezone)}}</li>
                            <hr>

                            <li>{{tr('updated_at')}} - {{common_date($post_details->updated_at , Auth::guard('admin')->user()->timezone)}}</li>
                            <hr>
                        </ul>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
  
    
@endsection

