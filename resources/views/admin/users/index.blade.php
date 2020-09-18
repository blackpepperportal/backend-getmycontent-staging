@extends('layouts.admin') 

@section('title', tr('users')) 

@section('content-header', tr('users')) 

@section('breadcrumb')

<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ tr('home') }}</a>
</li>

<li class="breadcrumb-item active"><a href="">{{ tr('users') }}</a>
</li>

<li class="breadcrumb-item">{{tr('view_users')}}</li>

@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_users') }}</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                    <div class="heading-elements">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('add_user') }}</a>
                    </div>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        <form method="GET" action="{{route('admin.users.index')}}">

                            <div class="row">

                                <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 resp-mrg-btm-md">
                                    @if(Request::has('search_key'))
                                        <p class="text-muted">Search results for <b>{{Request::get('search_key')}}</b></p>
                                    @endif
                                </div>

                                <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 md-full-width resp-mrg-btm-md">

                                    <select class="form-control select2" name="status">

                                        <option  class="select-color" value="">{{tr('select_status')}}</option>

                                        <option  class="select-color" value="{{SORT_BY_APPROVED}}">{{tr('approved')}}</option>

                                        <option  class="select-color" value="{{SORT_BY_DECLINED}}">{{tr('declined')}}</option>

                                        <option  class="select-color" value="{{SORT_BY_EMAIL_VERIFIED}}">{{tr('verified')}}</option>

                                        <option  class="select-color" value="{{SORT_BY_EMAIL_NOT_VERIFIED}}">{{tr('unverified')}}</option>

                                    </select>

                                </div>

                                <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">

                                    <div class="input-group">
                                       
                                        <input type="text" class="form-control" name="search_key"
                                        placeholder="{{tr('users_search_placeholder')}}"> <span class="input-group-btn">
                                        &nbsp

                                        <button type="submit" class="btn btn-default">
                                           <a href=""><i class="fa fa-search" aria-hidden="true"></i></a>
                                        </button>
                                        
                                        <button class="btn btn-default"><a  href="{{route('admin.users.index')}}"><i class="fa fa-eraser" aria-hidden="true"></i></button>
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
                                    <th>{{ tr('name') }}</th>
                                    <th>{{ tr('email') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('verify') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($users as $i => $user_details)

                                <tr>
                                    <td>{{ $i+$users->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $user_details->id] )  }}">
                                        {{ $user_details->name }}
                                        </a>
                                    </td>

                                    <td>{{ $user_details->email }}<br>
                                        <span class="text-success">{{ $user_details->mobile ?: "-" }}</span>
                                    </td>

                                    <td>
                                        @if($user_details->status == USER_APPROVED)

                                        <span class="btn btn-success btn-sm">{{ tr('approved') }}</span> @else

                                        <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span> @endif
                                    </td>

                                    <td>
                                        @if($user_details->is_verified == USER_EMAIL_NOT_VERIFIED)

                                        <a class="btn btn-outline-danger btn-sm" href="{{ route('admin.users.verify' , ['user_id' => $user_details->id]) }}">
                                            <i class="icon-close"></i> {{ tr('verify') }}
                                        </a>

                                        @else

                                        <span class="btn btn-success btn-sm">{{ tr('verified') }}</span> @endif
                                    </td>

                                    <td>
                                    
                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{ route('admin.users.view', ['user_id' => $user_details->id] ) }}">&nbsp;{{ tr('view') }}</a> 

                                                @if(Setting::get('is_demo_control_enabled') == YES)

                                                    <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('edit') }}</a>

                                                    <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a> 

                                                @else

                                                    <a class="dropdown-item" href="{{ route('admin.users.edit', ['user_id' => $user_details->id] ) }}">&nbsp;{{ tr('edit') }}</a>

                                                    <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('user_delete_confirmation' , $user_details->name) }}&quot;);" href="{{ route('admin.users.delete', ['user_id' => $user_details->id] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                @endif

                                                @if($user_details->status == APPROVED)

                                                    <a class="dropdown-item" href="{{  route('admin.users.status' , ['user_id' => $user_details->id] )  }}" onclick="return confirm(&quot;{{ $user_details->name }} - {{ tr('user_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                </a> 

                                                @else

                                                    <a class="dropdown-item" href="{{ route('admin.users.status' , ['user_id' => $user_details->id] ) }}">&nbsp;{{ tr('approve') }}</a> 

                                                @endif

                                                <hr>

                                                <a class="dropdown-item" href="{{ route('admin.orders.index', ['user_id' => $user_details->id] ) }}">&nbsp;{{ tr('orders') }}</a> 

                                                <a class="dropdown-item" href="{{ route('admin.post.payments', ['user_id' => $user_details->id] ) }}">&nbsp;{{ tr('post_payments') }}</a> 

                                                <a class="dropdown-item" href="{{ route('admin.delivery_address.index', ['user_id' => $user_details->id] ) }}">&nbsp;{{ tr('delivery_address') }}</a> 

                                            </div>

                                        </div>

                                    </td>

                                </tr>

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