@extends('layouts.admin') 

@section('content-header', tr('followings')) 

@section('breadcrumb')

<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ tr('home') }}</a>
</li>

<li class="breadcrumb-item active"><a href="{{route('admin.users.index')}}">{{ tr('users') }}</a>
</li>

<li class="breadcrumb-item">{{tr('followings')}}</li>

@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('followings') }}</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        <table class="table table-striped table-bordered sourced-data">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('user') }}</th>
                                    <th>{{ tr('follower') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('updated_at') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($followings as $i => $following_details)

                                <tr>
                                    <td>{{ $i+$followings->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $following_details->user_id] )  }}">
                                        {{ $following_details->userDetails->name ?? "-" }}
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{  route('admin.content_creators.view' , ['content_creator_id' => $following_details->follower_id] )  }}">
                                        {{ $following_details->contentCreatorDetails->name ?? "-"}}
                                        </a>
                                    </td>

                                    <td>
                                        @if($following_details->status == APPROVED)

                                        <span class="btn btn-success btn-sm">{{ tr('approved') }}</span> @else

                                        <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span> @endif
                                    </td>

                                    <td>{{common_date($following_details->updated_at , Auth::guard('admin')->user()->timezone)}}</td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $followings->appends(request()->input())->links() }}
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection