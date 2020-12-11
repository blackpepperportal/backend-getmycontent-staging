@extends('layouts.admin')

@section('title', tr('users'))

@section('content-header', tr('blocked_users'))

@section('breadcrumb')


<li class="breadcrumb-item active">
    <a href="{{route('admin.users.index')}}">{{ tr('users') }}</a>
</li>

<li class="breadcrumb-item">{{$title ?? tr('blocked_users')}}</li>

@endsection

@section('content')

<section id="configuration">
    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{$title ?? tr('view_users')}} - {{$user->name ?? ''}}


                    </h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i>

                    </a>

                    <div class="heading-elements">

                    </div>



                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard table-responsive">

                        <table class="table table-striped table-bordered sourced-data ">

                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('username') }}</th>
                                    <th>{{ tr('blocked_user') }}</th>
                                    <th>{{ tr('reason') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($blocked_users as $i => $user)

                                <tr>

                                    <td>{{ $i+$blocked_users->firstItem() }}</td>

                                    <td>
                                        {{$user->user->name ?? ''}}

                                    </td>

                                    <td>
                                        {{$user->blockeduser->name ?? ''}}
                                    </td>

                                    <td>
                                        {{$user->reason ?:tr('not_available')}}
                                    </td>

                                    <td>
                                        @if($user->status == USER_APPROVED)

                                        <span class="btn btn-success btn-sm">{{ tr('approved') }}</span>

                                        @else

                                        <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span>

                                        @endif
                                    </td>

                                    <td>

                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                @if($user->status == APPROVED)

                                                <a class="dropdown-item" href="{{  route('admin.block_users.status' , ['block_user_id' => $user->id,'status'=>DECLINED] )  }}" onclick="return confirm(&quot; {{ tr('decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                </a>

                                                @else

                                                <a class="dropdown-item" href="{{ route('admin.block_users.status' , ['block_user_id' => $user->id,'status'=>APPROVED] ) }}">&nbsp;{{ tr('approve') }}</a>

                                                @endif


                                                @if(Setting::get('is_demo_control_enabled') == YES)

                                                <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a>

                                                @else


                                                <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('delete_confirmation') }}&quot;);" href="{{ route('admin.block_users.delete', ['block_user_id' => $user->id,'page'=>request()->input('page')] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                @endif



                                            </div>

                                        </div>

                                    </td>

                                </tr>


                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right" id="paglink">{{ $blocked_users->appends(request()->input())->links() }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection