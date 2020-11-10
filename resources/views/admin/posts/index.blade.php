@extends('layouts.admin') 

@section('title', tr('posts')) 

@section('content-header', tr('posts')) 

@section('breadcrumb')



<li class="breadcrumb-item active">
    <a href="{{route('admin.posts.index')}}">{{ tr('posts') }}</a>
</li>

<li class="breadcrumb-item active">
    {{Request::get('scheduled') ? tr('scheduled_posts') : tr('view_posts')}}
</li>

@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">

                    @if(Request::get('scheduled'))

                        {{tr('scheduled_posts')}}

                    @else

                        {{ tr('view_posts') }}

                    @endif
                    </h4>

                    <div class="heading-elements">
                        <a href="{{ route('admin.posts.create') }}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('add_post') }}</a>
                    </div>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        @include('admin.posts._search')

                        <table class="table table-striped table-bordered sourced-data">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('content_creator') }}</th>
                                    <th>{{ tr('publish_time') }}</th>
                                    <th>{{ tr('amount') }}</th>
                                    <th>{{ tr('is_paid_post') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($posts as $i => $post)
                                <tr>
                                    <td>{{ $i+$posts->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.posts.view' , ['post_id' => $post->id] )  }}">
                                        {{ $post->userdisplayname ?? "-" }}
                                        </a>
                                    </td>

                                    <td>{{($post->publish_time) ? common_date($post->publish_time , Auth::guard('admin')->user()->timezone) : '-'}}</td>

                                    <td>
                                        {{ $post->amount_formatted}}
                                    </td>

                                    <td>
                                        @if($post->is_paid_post)
                                            <span class="badge badge-success">{{tr('yes')}}</span>
                                        @else
                                            <span class="badge badge-danger">{{tr('no')}}</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($post->status == APPROVED)

                                        <span class="btn btn-success btn-sm">{{ tr('approved') }}</span> @else

                                        <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span> @endif
                                    </td>


                                    <td>
                                    
                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                 <a class="dropdown-item" href="{{ route('admin.posts.dashboard', ['post_id' => $post->id] ) }}">&nbsp;{{ tr('dashboard') }}</a> 


                                                <a class="dropdown-item" href="{{ route('admin.posts.view', ['post_id' => $post->id] ) }}">&nbsp;{{ tr('view') }}</a> 

                                                 <a class="dropdown-item" href="{{ route('admin.posts.edit', ['post_id' => $post->id] ) }}">&nbsp;{{ tr('edit') }}</a> 

                                                @if(Setting::get('is_demo_control_enabled') == YES)

                                                  

                                                    <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a> 

                                                @else

                                                    <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('post_delete_confirmation' , $post->unique_id) }}&quot;);" href="{{ route('admin.posts.delete', ['post_id' => $post->id] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                @endif

                                                @if($post->status == APPROVED)

                                                    <a class="dropdown-item" href="{{  route('admin.posts.status' , ['post_id' => $post->id] )  }}" onclick="return confirm(&quot;{{ tr('post_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                </a> 

                                                @else

                                                    <a class="dropdown-item" href="{{ route('admin.posts.status' , ['post_id' => $post->id] ) }}">&nbsp;{{ tr('approve') }}</a> 

                                                @endif
        
                                                  <a class="dropdown-item" href="{{ route('admin.posts.comments' , ['post_id' => $post->id] ) }}">&nbsp;{{ tr('comments') }}</a> 
                                                @if($post->is_published == NO)
                                                  
                                                    <a class="dropdown-item" href="{{ route('admin.posts.publish' , ['post_id' => $post->id] ) }}">&nbsp;{{ tr('publish') }}</a> 

                                                @endif

                                            </div>

                                        </div>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $posts->appends(request()->input())->links() }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection