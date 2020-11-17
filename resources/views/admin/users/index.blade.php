@extends('layouts.admin')

@section('title', tr('users'))

@section('content-header', tr('users'))

@section('breadcrumb')
 

@if(!isset($title))
<li class="breadcrumb-item active">
    <a href="{{route('admin.users.index')}}">{{ tr('users') }}</a>
</li>
@endif

<li class="breadcrumb-item">{{$title ?? tr('view_users')}}</li>

@endsection

@section('content')

<section id="configuration">
    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{$title ?? tr('view_users')}}</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                    <div class="heading-elements">
                        <a href="{{ route('admin.users.excel',['downloadexcel'=>'excel','status'=>Request::get('status'),'searc_key'=>Request::get('search_key'),'account_type'=>Request::get('account_type')]) }}" class="btn btn-primary">Export to Excel</a>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('add_user') }}</a>
                    </div>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard table-responsive">

                        @include('admin.users._search')

                        <table class="table table-striped table-bordered sourced-data">

                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('name') }}</th>
                                    <th>{{ tr('email') }}</th>
                                    <th>{{ tr('user_wallets') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th><i class="icon-envelope"></i> {{ tr('verify') }}</th>
                                    <th>{{tr('documents')}}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($users as $i => $user)

                                <tr>
                                    <td>{{ $i+$users->firstItem() }}</td>

                                    <td>
                                        <a href="{{route('admin.users.view' , ['user_id' => $user->id])}}" class="custom-a">
                                            {{$user->name}}
                                        </a> 
                                        @if($user->user_account_type == USER_PREMIUM_ACCOUNT) 
                                        <b><i class="icon-badge text-green"></i></b>
                                        @endif
                                    </td>

                                    <td>{{ $user->email }}<br>
                                        <span class="custom-muted">{{ $user->mobile ?: "-" }}</span>
                                    </td>

                                    <td>
                                        <a href="{{route('admin.user_wallets.view' , ['user_id' => $user->id])}}">
                                            {{$user->userWallets->remaining_formatted ?? formatted_amount(0.00)}}
                                        </a>
                                    </td>

                                    <td>
                                        @if($user->status == USER_APPROVED)

                                            <span class="btn btn-success btn-sm">{{ tr('approved') }}</span> 

                                        @else

                                            <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span> 

                                        @endif
                                    </td>

                                    <td>
                                        @if($user->is_email_verified == USER_EMAIL_NOT_VERIFIED)

                                            <a class="btn btn-outline-danger btn-sm" href="{{ route('admin.users.verify', ['user_id' => $user->id]) }}">
                                                <i class="icon-close"></i> {{ tr('verify') }}
                                            </a>

                                        @else

                                            <span class="btn btn-success btn-sm">{{ tr('verified') }}</span> 

                                        @endif
                                    </td>

                                    <td>
                                        <span class="text-primary">
                                            <a class="btn btn-outline-blue" href="{{route('admin.user_documents.view', ['user_id' => $user->id])}}">{{$user->is_document_verified_formatted}}</a>

                                        </span>
                                    </td>

                                    <td>

                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{ route('admin.users.view', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('view') }}</a>
                                                
                                                    <a class="dropdown-item" href="{{ route('admin.users.view', ['user_id' => $user->id] ) }}" data-toggle="modal" data-target="#{{$user->id}}">&nbsp;{{ ($user->user_account_type  == USER_FREE_ACCOUNT) ? tr('upgrade_to_premium') : tr('update_premium') }}</a>

                                                @if(Setting::get('is_demo_control_enabled') == YES)

                                                <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('edit') }}</a>

                                                <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a>

                                                @else

                                                <a class="dropdown-item" href="{{ route('admin.users.edit', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('edit') }}</a>

                                                <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('user_delete_confirmation' , $user->name) }}&quot;);" href="{{ route('admin.users.delete', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                @endif

                                                @if($user->status == APPROVED)

                                                <a class="dropdown-item" href="{{  route('admin.users.status' , ['user_id' => $user->id] )  }}" onclick="return confirm(&quot;{{ $user->name }} - {{ tr('user_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                </a>

                                                @else

                                                <a class="dropdown-item" href="{{ route('admin.users.status' , ['user_id' => $user->id] ) }}">&nbsp;{{ tr('approve') }}</a>

                                                @endif

                                                <div class="dropdown-divider"></div>

                                                <a class="dropdown-item" href="{{ route('admin.user_followings', ['user_id' => $user->id]) }}">&nbsp;{{ tr('followings') }}</a>

                                                <a class="dropdown-item" href="{{ route('admin.user_followers', ['follower_id' => $user->id]) }}">&nbsp;{{ tr('followers') }}</a>

                                                <a class="dropdown-item" href="{{ route('admin.orders.index', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('orders') }}</a>

                                                <a class="dropdown-item" href="{{ route('admin.post.payments', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('post_payments') }}</a>

                                                <a class="dropdown-item" href="{{ route('admin.delivery_address.index', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('delivery_address') }}</a>


                                               <a class="dropdown-item" href="{{ route('admin.bookmarks.index', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('bookmarks') }}</a>

                                               <a class="dropdown-item" href="{{ route('admin.fav_users.index', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('favorite_users') }}</a>


                                                <a class="dropdown-item" href="{{ route('admin.post_likes.index', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('liked_posts') }}</a>

                                                <a class="dropdown-item" href="{{ route('admin.user_wallets.view', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('wallets') }}</a>


                                                
                                            </div>

                                        </div>

                                    </td>

                                </tr>
                                <!-- modal start -->

                                @include('admin.users._premium_account_form')
                                
                                <!-- Modal -->

                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right" id="paglink">{{ $users->appends(request()->input())->links() }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection