@extends('layouts.admin')

@section('title', tr('posts'))

@section('content-header', tr('posts'))

@section('breadcrumb')

    

    <li class="breadcrumb-item active"><a href="{{route('admin.posts.index')}}">{{tr('posts')}}</a>
    </li>

    <li class="breadcrumb-item">{{tr('post_dashboard')}}</li>

@endsection

@section('content')

<div class="content-body">

    <div class="col-12">

        <div class="card">

            <div class="card-header border-bottom border-gray">

                <h4 class="card-title">{{ tr('post_dashboard') }}</h4>
                <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-xl-3 col-lg-6 col-12">
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

                    <div class="col-xl-3 col-lg-6 col-12">
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

                     <div class="col-xl-3 col-lg-4 col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="media align-items-stretch">
                                    <div class="p-2 text-center bg-info bg-darken-2">
                                        <i class="icon-like font-large-2 white"></i>
                                    </div>
                                    <div class="p-2 bg-gradient-x-info white media-body">
                                        <h5>{{tr('total_likes')}}</h5>
                                        <h5 class="text-bold-400 mb-0">{{$number_of_likes}}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-4 col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="media align-items-stretch">
                                    <div class="p-2 text-center bg-danger bg-darken-2">
                                        <i class="icon-heart font-large-2 white"></i>
                                    </div>
                                    <div class="p-2 bg-gradient-x-danger white media-body">
                                        <h5>{{tr('tips_earnings')}}</h5>
                                        <h5 class="text-bold-400 mb-0">{{formatted_amount($payment_data->tips_earnings ?? 0.00)}}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                  


                <div class="row match-height">

                    <div class="col-xl-12 col-lg-12">

                        <div class="recent_comment">

                            <div class="card-body">

                                <div class="card-header">

                                    <div class="d-flex justify-content-between">

                                        <h4 class="card-title">{{tr('recent_comments')}}</h4>

                                    </div>

                                </div>

                                @forelse($data->recent_comments as $i => $recent_comment)

                                <a href="{{ route('admin.users.view', ['user_id' => $recent_comment->user_id])}}" class="nav-link">

                                    <div class="wrapper d-flex align-items-center py-2 border-bottom">

                                        <img class="img-sm rounded-circle" src="{{ $recent_comment->userpicture }}" alt="profile">

                                        <div class="wrapper ml-3">
                                            <h6 class="ml-1 mb-1">
                                                {{$recent_comment->username}} 
                                            </h6>

                                            <small class="text-muted mb-0">
                                                <i class="fa fa-comments mr-1"></i>
                                                {{ $recent_comment->comment }}

                                            </small>
                                            <br>

                                        </div>

                                        <small class="text-muted ml-auto">{{common_date($recent_comment->created_at , Auth::guard('admin')->user()->timezone)}}</small>
                                    </div>
                                </a>

                                @empty
                                <div class="text-center m-2">
                                    <h2 class="text-muted">
                                        <i class="fa fa-comments"></i>
                                    </h2>
                                    <p>{{tr('no_result_found')}}</p> 
                                </div>
                               
                                @endforelse

                                @if($data->recent_comments->count() > 5)

                                <p align="center">
                                    <a href="{{route('admin.posts.comments', ['post_id' => $recent_comment->post_id])}}" class="text-uppercase btn btn-success btn-xs wrapper">{{tr('view_all')}}</a>
                                </p>

                                @endif

                               
                            </div>

                        </div>

                    </div>
                </div>

        </div>

    </div>

</div>
  
    
@endsection

