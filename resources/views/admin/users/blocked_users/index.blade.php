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

                    </div>



                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        <table class="table table-striped table-bordered sourced-data table-responsive">

                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('name') }}</th>
                                    <th>{{ tr('blocked_count') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($block_users as $i => $user)

                                <tr>

                                    <td>{{ $i+$block_users->firstItem() }}</td>

                                    <td>
                                        <a href="{{route('admin.users.view' , ['user_id' => $user->blocked_to])}}" class="custom-a">
                                            {{$user->blockeduser->name ?? ''}}
                                        </a>

                                    </td>

                                    <td>
                                         <a href="{{route('admin.block_users.view' , ['user_id' => $user->blocked_to])}}" class="custom-a">
                                          {{$user->blocked_count ?? ''}}
                                         </a>
                                    </td>

                                    <td>

                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{ route('admin.block_users.view', ['user_id' => $user->blocked_to] ) }}">&nbsp;{{ tr('view') }}</a>

                                                @if($user->blockeduser->status == APPROVED)

                                                <a class="dropdown-item" href="{{  route('admin.users.status' , ['user_id' => $user->blocked_to] )  }}" onclick="return confirm(&quot;{{ $user->blockeduser->name ?? '' }} - {{ tr('user_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                </a>

                                                @else

                                                <a class="dropdown-item" href="{{ route('admin.users.status' , ['user_id' => $user->blocked_to] ) }}">&nbsp;{{ tr('approve') }}</a>

                                                @endif

                                                <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('user_delete_confirmation' , $user->blockeduser->name ?? '') }}&quot;);" href="{{ route('admin.users.delete', ['user_id' => $user->blocked_to,'page'=>request()->input('page')] ) }}">&nbsp;{{ tr('delete') }}</a>


                                                <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('delete_confirmation' , $user->blockeduser->name ?? '') }}&quot;);" href="{{ route('admin.block_users.delete', ['user_id' => $user->blocked_to,'page'=>request()->input('page')] ) }}">&nbsp;{{ tr('delete_report') }}</a>

                                            </div>

                                        </div>

                                    </td>

                                </tr>


                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right" id="paglink">{{ $block_users->appends(request()->input())->links() }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection