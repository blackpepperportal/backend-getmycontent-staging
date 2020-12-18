@extends('layouts.admin')

@section('title', tr('posts'))

@section('content-header', tr('posts'))

@section('breadcrumb')



<li class="breadcrumb-item active">
    <a href="{{route('admin.posts.index')}}">{{ tr('posts') }}</a>
</li>

<li class="breadcrumb-item active">
    {{tr('view_report_posts')}}
</li>

@endsection

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">

                        {{ tr('view_report_posts') }}

                    </h4>

                    <div class="heading-elements">


                    </div>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">


                        <table class="table table-striped table-bordered sourced-data">

                            <thead>
                                <tr>

                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('post') }}</th>
                                    <th>{{ tr('report_user_count') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($report_posts as $i => $post)
                                <tr>
                                    <td>{{ $i+$report_posts->firstItem() }}</td>

                                    <td>

                                        <a href="{{route('admin.posts.view',['post_id'=>$post->post->id ?? ''])}}">
                                            {{$post->post->post_unique_id ?? '' }}
                                        </a>

                                    </td>

                                    <td>
                                        <a class="custom-a" href="{{route('admin.report_posts.view',['post_id'=>$post->post_id])}}">
                                            {{$post->report_user_count }}
                                        </a>
                                    </td>

                                    <td>

                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{route('admin.report_posts.view',['post_id'=>$post->post_id])}}">&nbsp;{{ tr('view') }}</a>

                                                @if($post->post->status == APPROVED)

                                                <a class="dropdown-item" href="{{  route('admin.posts.status' , ['post_id' => $post->post_id] )  }}" onclick="return confirm(&quot;{{ tr('post_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                </a>

                                                @else

                                                <a class="dropdown-item" href="{{ route('admin.posts.status' , ['post_id' => $post->post_id] ) }}">&nbsp;{{ tr('approve') }}</a>

                                                @endif

                                                <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('post_delete_confirmation' , $post->unique_id) }}&quot;);" href="{{ route('admin.posts.delete', ['post_id' => $post->post_id,'page'=>request()->input('page')] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                <a class="dropdown-item" onclick="return confirm(&quot; {{ tr('delete_confirmation') }}&quot;);" href="{{route('admin.report_posts.delete',['post_id'=>$post->post_id])}}">&nbsp;{{ tr('delete_report') }}</a>

                                            </div>
                                        </div>
                                    </td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right" id="paglink">{{ $report_posts->appends(request()->input())->links() }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection