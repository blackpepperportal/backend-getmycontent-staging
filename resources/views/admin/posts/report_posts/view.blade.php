@extends('layouts.admin')

@section('title', tr('posts'))

@section('content-header', tr('report_posts'))

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

                        {{ tr('view_report_posts') }} -

                        <a href="{{route('admin.posts.view',['post_id'=>$post->id])}}">{{$post->post_unique_id}}</a>

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
                                    <th>{{ tr('report_user') }}</th>
                                    <th>{{ tr('reason') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($report_posts as $i => $report_post)
                                <tr>
                                    <td>{{ $i+$report_posts->firstItem() }}</td>

                                    <td>

                                        <a href="{{route('admin.posts.view',['post_id'=>$report_post->post->id ?? ''])}}">
                                            {{$report_post->post->post_unique_id }}
                                        </a>

                                    </td>

                                    <td>
                                        <a href="{{route('admin.users.view',['user_id'=>$report_post->block_by ?? ''])}}">
                                            {{$report_post->blockeduser->name ?? '' }}
                                        </a>
                                    </td>

                                    <td>{{$report_post->reason ?? '' }}</td>


                                    <td>

                                        <a class="btn btn-outline-warning btn-large" onclick="return confirm(&quot; {{ tr('delete_confirmation') }}&quot;);" href="{{route('admin.report_posts.delete',['report_post_id'=>$report_post->id])}}">&nbsp;{{ tr('delete') }}</a>

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