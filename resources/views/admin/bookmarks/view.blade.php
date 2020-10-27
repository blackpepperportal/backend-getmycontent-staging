@extends('layouts.admin')

@section('title', tr('bookmarks'))

@section('content-header', tr('bookmarks'))

@section('breadcrumb')

    
    <li class="breadcrumb-item"><a href="{{route('admin.users.index')}}">{{tr('users')}}</a>
    </li>
    <li class="breadcrumb-item active">{{tr('view_bookmarks')}}</a>
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
                                    <td>{{$bookmarks_details->user->name ?? "-"}}</td>
                                </tr>

                                <tr>
                                    <th>{{tr('post_details')}}</th>
                                    <td>{{$bookmarks_details->post->content}}</td>
                                </tr> 

                                <tr>
                                    <th>{{tr('status')}}</th>
                                    @if($bookmarks_details->status == APPROVED)
                                    <td>{{ tr('approved') }}</td>
                                    @else
                                    <td>{{ tr('declined') }}</td>
                                    @endif
                                </tr>
                                
                                <tr>
                                    <th>{{tr('created_at')}}</th>
                                    <td>{{ common_date($bookmarks_details->created_at,Auth::guard('admin')->user()->timezone) }}</td>
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
