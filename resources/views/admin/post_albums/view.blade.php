@extends('layouts.admin')

@section('title', tr('post_albums'))

@section('content-header', tr('post_albums'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{tr('home')}}</a>
    </li>

    <li class="breadcrumb-item active"><a href="{{route('admin.post_albums.index')}}">{{tr('post_albums')}}</a>
    </li>

    <li class="breadcrumb-item">{{tr('view_post_albums')}}</li>

@endsection

@section('content')

<div class="content-body">

    <div class="col-12">

        <div class="card">

            <div class="card-header border-bottom border-gray">

                <h4 class="card-title">{{ tr('view_post_albums') }}</h4>
                <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                
            </div>

            <div class="text-center">

                <div class="card-body">
                    <img src="{{$post_album_details->stardomDetails->picture ?? asset('placeholder.jpg')}}" class="rounded-circle height-100" alt="Card image" />
                </div>

                <div class="card-body">
                    <h4 class="card-title">{{$post_album_details->stardomDetails->name ?? "-"}}</h4>
                    <h6 class="card-subtitle text-muted">{{$post_album_details->stardomDetails->email ?? "-"}}</h6>
                </div>

                <div class="text-center">

                    <a href="{{route('admin.stardoms.view',['stardom_id' => $post_album_details->stardom_id])}}" class="btn btn-primary">
                        {{tr('go_to_profile')}}
                    </a>

                    @if(Setting::get('is_demo_control_enabled') == YES)

                        <a class="btn btn-danger" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a> 

                    @else

                        <a class="btn btn-danger" onclick="return confirm(&quot;{{ tr('post_album_delete_confirmation' , $post_album_details->name) }}&quot;);" href="{{ route('admin.post_albums.delete', ['post_album_id' => $post_album_details->id] ) }}">&nbsp;{{ tr('delete') }}</a>

                    @endif

                    @if($post_album_details->status == APPROVED)

                        <a class="btn btn-danger" href="{{  route('admin.post_albums.status' , ['post_album_id' => $post_album_details->id] )  }}" onclick="return confirm(&quot;{{ tr('post_album_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                    </a> 

                    @else

                        <a class="btn btn-success" href="{{ route('admin.post_albums.status' , ['post_album_id' => $post_album_details->id] ) }}">&nbsp;{{ tr('approve') }}</a> 

                    @endif
                   
                </div>

                <hr>

            </div>

            <div class="row">

                <div class="col-6">
                    
                    <ul>
                        <li class="text-uppercase">{{tr('unique_id')}} - {{$post_album_details->unique_id}}</li>
                        <hr>

                        <li>{{tr('name')}} - {{$post_album_details->name}}</li>
                        <hr>

                        <li>{{tr('status')}} - 

                            @if($post_album_details->status == APPROVED)

                                <span class="btn btn-success btn-sm">{{ tr('approved') }}</span> 
                            @else

                                <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span> 
                            @endif
                        </li>
                        <hr>

                        <li>{{tr('created_at')}} - {{common_date($post_album_details->created_at , Auth::guard('admin')->user()->timezone)}}</li>
                        <hr>

                        <li>{{tr('updated_at')}} - {{common_date($post_album_details->updated_at , Auth::guard('admin')->user()->timezone)}}</li>
                        <hr>

                    </ul>
                </div>

                <div class="col-6">
                    <ul>
                        <b>{{tr('posts_content')}}</b>

                        @foreach($posts as $post_details)

                            <li>{{$post_details->content ?? "-"}}</li>
                            <hr>

                        @endforeach

                    </ul>
                </div>

            </div>

        </div>

    </div>

</div>
  
@endsection
