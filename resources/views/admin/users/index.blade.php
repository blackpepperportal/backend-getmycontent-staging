@extends('layouts.admin')

@section('title', tr('users'))

@section('content-header', tr('users'))

@section('breadcrumb')


<li class="breadcrumb-item active">
    <a href="{{route('admin.users.index')}}">{{ tr('users') }}</a>
</li>

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

                       @if($users->count() >= 1)
                        <a class="btn btn-primary  dropdown-toggle  bulk-action-dropdown" href="#" id="dropdownMenuOutlineButton2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-plus"></i> {{tr('bulk_action')}}
                        </a>
                       @endif


                        <a href="{{ route('admin.users.excel',['downloadexcel'=>'excel','status'=>Request::get('status'),'searc_key'=>Request::get('search_key'),'account_type'=>Request::get('account_type')]) }}" class="btn btn-primary">Export to Excel</a>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('add_user') }}</a>

                        <div class="dropdown-menu float-right" aria-labelledby="dropdownMenuOutlineButton2">

                            <a class="dropdown-item action_list" href="#" id="bulk_delete">
                                {{tr('delete')}}
                            </a>

                            <a class="dropdown-item action_list" href="#" id="bulk_approve">
                                {{ tr('approve') }}
                            </a>

                            <a class="dropdown-item action_list" href="#" id="bulk_decline">
                                {{ tr('decline') }}
                            </a>
                        </div>

                        <div class="bulk_action">

                            <form action="{{route('admin.users.bulk_action')}}" id="users_form" method="POST" role="search">

                                @csrf

                                <input type="hidden" name="action_name" id="action" value="">

                                <input type="hidden" name="selected_users" id="selected_ids" value="">

                                <input type="hidden" name="page_id" id="page_id" value="{{ (request()->page) ? request()->page : '1' }}">

                            </form>

                        </div>

                    </div>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard table-responsive">

                        @include('admin.users._search')

                        <table class="table table-striped table-bordered sourced-data ">

                            <thead>
                                <tr>
                                    @if($users->count() >= 1)
                                    <th>
                                        <input id="check_all" type="checkbox" class="chk-box-left">
                                    </th>
                                    @endif
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('name') }}</th>
                                    <th>{{ tr('email') }}</th>
                                    <th>{{ tr('user_wallets') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th><i class="icon-envelope"></i> {{ tr('verify') }}</th>

                                    @if(Setting::get('is_verified_badge_enabled'))
                                    <th>{{tr('is_badge_verified')}}</th>
                                    @endif

                                    <th>{{tr('documents')}}</th>

                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($users as $i => $user)

                                <tr>
                                    
                                    <td id="check{{$user->id}}"><input type="checkbox" name="row_check" class="faChkRnd chk-box-inner-left" id="{{$user->id}}" value="{{$user->id}}"></td>

                                    <td>{{ $i+$users->firstItem() }}</td>

                                    <td>
                                        <a href="{{route('admin.users.view' , ['user_id' => $user->id])}}" class="custom-a">
                                            {{$user->name}}
                                        </a>
                                        @if($user->user_account_type == USER_PREMIUM_ACCOUNT)
                                        <b><i class="icon-badge text-green"></i></b>
                                        @endif
                                        @if(Cache::has($user->id))
                                            <span class="text-success">Online</span>
                                        @else
                                            <span class="text-secondary">Offline</span>
                                        @endif
                                    </td>

                                    <td>{{ $user->email }}<br>
                                        <span class="custom-muted">{{ $user->mobile ?: tr('not_available') }}</span>
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

                                    @if(Setting::get('is_verified_badge_enabled'))
                                       
                                        <td>
                                            @if($user->is_verified_badge == YES)

                                            <span class="badge badge-success">{{tr('yes')}}</span>

                                            @else
                                            <span class="badge badge-danger">{{tr('no')}}</span>

                                            @endif
                                        </td>
                                        
                                    @endif
                                    
                                    <td>
                                        <a class="btn btn-blue btn-sm" href="{{route('admin.user_documents.view', ['user_id' => $user->id])}}">{{$user->is_document_verified_formatted}}</a>
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

                                                <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('user_delete_confirmation' , $user->name) }}&quot;);" href="{{ route('admin.users.delete', ['user_id' => $user->id,'page'=>request()->input('page')] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                @endif

                                                @if($user->status == APPROVED)

                                                <a class="dropdown-item" href="{{  route('admin.users.status' , ['user_id' => $user->id] )  }}" onclick="return confirm(&quot;{{ $user->name }} - {{ tr('user_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                </a>

                                                @else

                                                <a class="dropdown-item" href="{{ route('admin.users.status' , ['user_id' => $user->id] ) }}">&nbsp;{{ tr('approve') }}</a>

                                                @endif

                                                <div class="dropdown-divider"></div>

                                                <a class="dropdown-item" href="{{ route('admin.user_followings', ['user_id' => $user->id]) }}">&nbsp;{{ tr('followings') }}</a>

                                                <a class="dropdown-item" href="{{ route('admin.user_followers', ['follower_id' => $user->id]) }}">&nbsp;{{ tr('following') }}</a>

                                                <a class="dropdown-item" href="{{ route('admin.orders.index', ['user_id' => $user->id] ) }}" style="display: none;">&nbsp;{{ tr('orders') }}</a>

                                                <a class="dropdown-item" href="{{ route('admin.post.payments', ['user_id' => $user->id] ) }}">&nbsp;{{ tr('post_payments') }}</a>

                                                <a class="dropdown-item" href="{{ route('admin.delivery_address.index', ['user_id' => $user->id] ) }}" style="display: none;">&nbsp;{{ tr('delivery_address') }}</a>

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

@section('scripts')

@if(Session::has('bulk_action'))
<script type="text/javascript">
    $(document).ready(function() {
        localStorage.clear();
    });
</script>
@endif

<script type="text/javascript">
    $(document).ready(function() {
        get_values();

        // Call to Action for Delete || Approve || Decline
        $('.action_list').click(function() {
            var selected_action = $(this).attr('id');
            if (selected_action != undefined) {
                $('#action').val(selected_action);
                if ($("#selected_ids").val() != "") {
                    if (selected_action == 'bulk_delete') {
                        var message = "{{ tr('admin_users_delete_confirmation') }}";
                    } else if (selected_action == 'bulk_approve') {
                        var message = "{{ tr('admin_users_approve_confirmation') }}";
                    } else if (selected_action == 'bulk_decline') {
                        var message = "{{ tr('admin_users_decline_confirmation') }}";
                    }
                    var confirm_action = confirm(message);

                    if (confirm_action == true) {
                        $("#users_form").submit();
                    }
                    // 
                } else {
                    alert('Please select the check box');
                }
            }
        });
        // single check
        var page = $('#page_id').val();
        $('.faChkRnd:checkbox[name=row_check]').on('change', function() {

            var checked_ids = $(':checkbox[name=row_check]:checked').map(function() {
                    return this.id;
                })
                .get();

            localStorage.setItem("user_checked_items" + page, JSON.stringify(checked_ids));

            get_values();

        });
        // select all checkbox
        $("#check_all").on("click", function() {
            if ($("input:checkbox").prop("checked")) {
                $("input:checkbox[name='row_check']").prop("checked", true);
                var checked_ids = $(':checkbox[name=row_check]:checked').map(function() {
                        return this.id;
                    })
                    .get();
                // var page = {!! $users->lastPage() !!};
                console.log("user_checked_items" + page);

                localStorage.setItem("user_checked_items" + page, JSON.stringify(checked_ids));
                get_values();
            } else {
                $("input:checkbox[name='row_check']").prop("checked", false);
                localStorage.removeItem("user_checked_items" + page);
                get_values();
            }

        });

        // Get Id values for selected User
        function get_values() {
            var pageKeys = Object.keys(localStorage).filter(key => key.indexOf('user_checked_items') === 0);
            var values = Array.prototype.concat.apply([], pageKeys.map(key => JSON.parse(localStorage[key])));

            if (values) {
                $('#selected_ids').val(values);
            }

            for (var i = 0; i < values.length; i++) {
                $('#' + values[i]).prop("checked", true);
            }
        }



    });

  // to accept trailing zeroes
    $(document).ready(function(){
        $('.non_zero').on('input change', function (e) {
            var reg = /^0+/gi;
            if (this.value.match(reg)) {
                this.value = this.value.replace(reg, '');
            }
        });
     });

    $(document).ready(function (e) {

    $(".card-dashboard").scroll(function () {
        if($('.chk-box-inner-left').length <= 5){
            $(this).removeClass('table-responsive');
        }
    });

  });

</script>

@endsection