@extends('layouts.admin')

@section('title', tr('bookmarks'))

@section('content-header', tr('bookmarks'))

@section('breadcrumb')
    
    <li class="breadcrumb-item">
        <a href="{{route('admin.users.index')}}">{{tr('users')}}</a>
    </li>

    <li class="breadcrumb-item">
        <a href="{{route('admin.bookmarks.index', ['user_id' => $post_bookmark->user_id])}}">{{tr('bookmarks')}}</a>
    </li>

    <li class="breadcrumb-item active">
        {{tr('view_bookmarks')}}
    </li>

@endsection

@section('content')

<div class="content-body">

    <div id="user-profile">

        <div class="row">
  
            <div class="col-xl-12 col-lg-12">

                <div class="card">

                    <div class="card-header border-bottom border-gray">

                        <h4 class="card-title">{{tr('view_bookmarks')}}</h4>

                    </div>

                    <div class="card-content">

                        <div class="table-responsive">

                            <table class="table table-xl mb-0">
                                <tr>
                                    <th>{{tr('username')}}</th>
                                    <td>{{$post_bookmark->username ?? "-"}}</td>
                                </tr>

                                <tr>
                                    <th>{{tr('post_details')}}</th>
                                    <td>{{$post_bookmark->post->content ?? tr('n_a')}}</td>
                                </tr> 
                                
                                <tr>
                                    <th>{{tr('created_at')}}</th>
                                    <td>{{ common_date($post_bookmark->created_at,Auth::guard('admin')->user()->timezone) }}</td>
                                </tr>

                                <tr>
                                    <th>{{tr('updated_at')}}</th>
                                    <td>{{ common_date($post_bookmark->updated_at,Auth::guard('admin')->user()->timezone) }}</td>
                                </tr>

                           
                            </table>

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
