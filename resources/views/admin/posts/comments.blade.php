@extends('layouts.admin') 

@section('title', tr('comments_list')) 

@section('content-header', tr('comments_list')) 

@section('breadcrumb')



<li class="breadcrumb-item active">
    <a href="{{route('admin.posts.index')}}">{{ tr('posts') }}</a>
</li>

<li class="breadcrumb-item active">
    {{tr('comments_list')}}
</li>

@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">

                        {{ tr('comments_list') }} - <a href="{{route('admin.users.view',['user_id'=>$post->user->id ?? ''])}}">{{ $post->user->name ?? '-'}}</a>

                    </h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">
                        <form method="GET" action="{{route('admin.posts.comments')}}">

                            <div class="row">

                                <input type="hidden" name="post_id" value="{{$post_id}}">

                                <div class="col-xs-12 col-sm-12 col-lg-2 col-md-6 resp-mrg-btm-md">
                                   
                                </div>


                                <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12 mx-auto">

                                    <div class="input-group form-margin-left-sm">

                                        <input type="text" class="form-control" name="search_key"
                                        placeholder="{{tr('comment_search_placeholder')}}"> <span class="input-group-btn">
                                            &nbsp

                                            <button type="submit" class="btn btn-default">
                                                <a href=""><i class="fa fa-search" aria-hidden="true"></i></a>
                                            </button>

                                            <button class="btn btn-default"><a  href="{{route('admin.posts.comments')}}"><i class="fa fa-eraser" aria-hidden="true"></i></button>
                                            </a>

                                        </span>

                                    </div>

                                </div>

                            </div>

                        </form>
                        <br>

                        <table class="table table-striped table-bordered sourced-data">

                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('username') }}</th>
                                    <th>{{ tr('comments') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($post_comments as $i => $post_comment)
                                <tr>
                                    <td>{{ $i+$post_comments->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $post_comment->user_id] )  }}">
                                            {{ $post_comment->username ?? "-" }}
                                        </a>
                                    </td>
                                   

                                        <td>
                                            {{ $post_comment->comment}}
                                        </td>



                                        <td>

                                            <div class="btn-group" role="group">

                                                <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">


                                                    @if(Setting::get('is_demo_control_enabled') == YES)



                                                    <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a> 

                                                    @else

                                                    <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('post_comment_delete_confirmation') }}&quot;);" href="{{ route('admin.post_comment.delete', ['comment_id' => $post_comment->id, 'post_id' => $post_comment->post_id] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                    @endif


                                                </div>

                                            </div>

                                        </td>

                                    </tr>

                                    @endforeach

                                </tbody>

                            </table>

                            <div class="pull-right" id="paglink">{{ $post_comments->appends(request()->input())->links() }}</div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    @endsection