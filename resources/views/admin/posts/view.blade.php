@extends('layouts.admin')

@section('title', tr('posts'))

@section('content-header', tr('posts'))

@section('breadcrumb')



<li class="breadcrumb-item active"><a href="{{route('admin.posts.index')}}">{{tr('posts')}}</a>
</li>

<li class="breadcrumb-item">{{tr('view_posts')}}</li>

@endsection

@section('content')

<div class="content-body">

    <div class="col-12">

        <div class="card">

            <div class="card-header border-bottom border-gray">

                <h4 class="card-title">{{ tr('view_posts') }}</h4>
                <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

            </div>

            <div class="card-body">

                <div class="row">

                
                    <div class="col-2">

                        <img src="{{$post->user->picture ?? asset('placeholder.jpg')}}" class="post-image" alt="Card image" />

                    </div>

                    <div class="col-4">

                        <h4 class="card-title">{{$post->user->name ?? "-"}}</h4>

                        <h6 class="card-subtitle text-muted">{{$post->user->email ?? "-"}}</h6>
                        <br>

                        <a href="{{route('admin.users.view',['user_id' => $post->user_id])}}" class="btn btn-primary">
                            {{tr('go_to_profile')}}
                        </a>

                        <a href="{{route('admin.post.payments',['post_id'=>$post->id])}}" class="btn btn-purple">{{tr('payments')}}</a>

                    </div>

                    <div class="col-3"></div>
                    
                    <div class="col-3">

                        <h4 class="card-title">{{tr('post')}}</h4><br>

                        @if(Setting::get('is_demo_control_enabled') == YES)

                        <a class="btn-sm btn-danger" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a>

                        @else

                        <a class="btn-sm btn-danger" onclick="return confirm(&quot;{{ tr('post_delete_confirmation' , $post->unique_id) }}&quot;);" href="{{ route('admin.posts.delete', ['post_id' => $post->id] ) }}">&nbsp;{{ tr('delete') }}</a>

                        @endif

                        @if($post->status == APPROVED)

                        <a class="btn-sm btn-secondary" href="{{  route('admin.posts.status' , ['post_id' => $post->id] )  }}" onclick="return confirm(&quot;{{ tr('post_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                        </a>

                        @else

                        <a class="btn-sm btn-success" href="{{ route('admin.posts.status' , ['post_id' => $post->id] ) }}">&nbsp;{{ tr('approve') }}</a>

                        @endif
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
                                        <h5 class="text-bold-400 mb-0">{{formatted_amount($payment_data->total_earnings)}}</h5>
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
                                        <h5 class="text-bold-400 mb-0">{{formatted_amount($payment_data->current_month_earnings)}}</h5>
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
                                        <h5 class="text-bold-400 mb-0">{{formatted_amount($payment_data->today_earnings)}}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row">



                    <div class="col-6">

                        <ul class="post-left">
                            <li class="text-uppercase">{{tr('unique_id')}} - {{$post->unique_id}}</li>
                            <hr>

                            <li>{{tr('publish_time')}} - {{common_date($post->publish_time , Auth::guard('admin')->user()->timezone)}}</li>
                            <hr>

                            <li>{{tr('is_paid_post')}} - @if($post->is_paid_post)
                                <span class="badge badge-success">{{tr('yes')}}</span>
                                @else
                                <span class="badge badge-danger">{{tr('no')}}</span>
                                @endif
                            </li>
                            <hr>

                            <li>{{tr('amount')}}- {{$post->amount_formatted}}</li>
                            <hr>

                        </ul>
                    </div>

                    <div class="col-6">

                        <ul>
                            <li>{{tr('content')}}-{{$post->content}}</li>
                            <hr>

                            <li>{{tr('status')}} -

                                @if($post->status == APPROVED)

                                <span class="btn btn-success btn-sm">{{ tr('approved') }}</span>
                                @else

                                <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span>
                                @endif
                            </li>
                            <hr>

                            <li>{{tr('created_at')}} - {{common_date($post->created_at , Auth::guard('admin')->user()->timezone)}}</li>
                            <hr>

                            <li>{{tr('updated_at')}} - {{common_date($post->updated_at , Auth::guard('admin')->user()->timezone)}}</li>
                            <hr>
                        </ul>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


@endsection