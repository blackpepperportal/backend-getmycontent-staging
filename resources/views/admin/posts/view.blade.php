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

            <div class="text-center">

                <div class="card-body">
                    <img src="{{$post_details->getStardomDetails->picture ?? asset('placeholder.jpg')}}" class="rounded-circle height-100" alt="Card image" />
                </div>

                <div class="card-body">
                    <h4 class="card-title">{{$post_details->getStardomDetails->name ?? "-"}}</h4>
                    <h6 class="card-subtitle text-muted">{{$post_details->getStardomDetails->email ?? "-"}}</h6>
                </div>

                <div class="text-center">

                    <a href="{{route('admin.stardoms.view',['stardom_id' => $post_details->stardom_id])}}" class="btn btn-primary">
                        {{tr('go_to_profile')}}
                    </a>
                   
                </div>

                <hr>

            </div>

            <div class="row">

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

                        <li>{{tr('amount')}}- {{formatted_amount($post_details->amount)}}</li>
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
  
    
@endsection

@section('scripts')

@endsection
