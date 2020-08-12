@extends('layouts.admin') 

@section('title', tr('stardoms')) 

@section('content-header', tr('stardoms')) 

@section('breadcrumb')

<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ tr('home') }}</a>
</li>
<li class="breadcrumb-item active">{{ tr('stardoms') }}</a>
</li>

@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('stardoms') }}</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                    <div class="heading-elements">
                        <a href="{{ route('admin.stardoms.create') }}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('add_stardom') }}</a>
                    </div>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        <form method="GET" action="{{route('admin.stardoms.index')}}">

                            <div class="row">

                                <div class="col-3">
                                    @if(Request::has('search_key'))
                                        <p class="text-muted">{{tr('search_results_for')}}<b>{{Request::get('search_key')}}</b></p>
                                    @endif
                                </div>

                                <div class="col-3">

                                    <select class="form-control select2" name="status">

                                        <option  class="select-color" value="">{{tr('select_status')}}</option>

                                        <option  class="select-color" value="{{SORT_BY_APPROVED}}">{{tr('approved')}}</option>

                                        <option  class="select-color" value="{{SORT_BY_DECLINED}}">{{tr('declined')}}</option>

                                        <option  class="select-color" value="{{SORT_BY_EMAIL_VERIFIED}}">{{tr('verified')}}</option>

                                        <option  class="select-color" value="{{SORT_BY_EMAIL_NOT_VERIFIED}}">{{tr('un_verified')}}</option>

                                    </select>

                                </div>

                                <div class="col-6">

                                    <div class="input-group">
                                       
                                        <input type="text" class="form-control" name="search_key"
                                        placeholder="{{tr('stardoms_search_placeholder')}}"> <span class="input-group-btn">
                                        &nbsp

                                        <button type="submit" class="btn btn-default">
                                           <a href=""><i class="fa fa-search" aria-hidden="true"></i></a>
                                        </button>
                                        
                                        <button class="btn btn-default"><a  href="{{route('admin.stardoms.index')}}"><i class="fa fa-eraser" aria-hidden="true"></i></button>
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
                                    <th>{{ tr('mobile') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('verify') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($stardoms as $i => $stardom_details)
                                <tr>
                                    <td>{{ $i+1 }}</td>

                                    <td>
                                        <a href="{{  route('admin.stardoms.view' , ['stardom_id' => $stardom_details->id] )  }}">
                                        {{ $stardom_details->name }}
                                        </a>
                                    </td>

                                    <td>{{ $stardom_details->email }}</td>

                                    <td>{{ $stardom_details->mobile ?: "-" }}</td>

                                    <td>
                                        @if($stardom_details->status == STARDOM_APPROVED)

                                        <span class="btn btn-success btn-sm">{{ tr('approved') }}</span> @else

                                        <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span> @endif
                                    </td>

                                    <td>
                                        @if($stardom_details->is_verified == STARDOM_EMAIL_NOT_VERIFIED)

                                        <a class="btn btn-outline-danger btn-sm" href="{{ route('admin.stardoms.verify' , ['stardom_id' => $stardom_details->id]) }}">
                                            <i class="icon-close"></i> {{ tr('verify') }}
                                        </a>

                                        @else

                                        <span class="btn btn-success btn-sm">{{ tr('verified') }}</span> @endif
                                    </td>

                                    <td>
                                    
                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{ route('admin.stardoms.view', ['stardom_id' => $stardom_details->id] ) }}">&nbsp;{{ tr('view') }}</a> 

                                                @if(Setting::get('is_demo_control_enabled') == YES)

                                                    <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('edit') }}</a>

                                                    <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a> 

                                                @else

                                                    <a class="dropdown-item" href="{{ route('admin.stardoms.edit', ['stardom_id' => $stardom_details->id] ) }}">&nbsp;{{ tr('edit') }}</a>

                                                    <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('stardom_delete_confirmation' , $stardom_details->name) }}&quot;);" href="{{ route('admin.stardoms.delete', ['stardom_id' => $stardom_details->id] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                @endif

                                                @if($stardom_details->status == APPROVED)

                                                    <a class="dropdown-item" href="{{  route('admin.stardoms.status' , ['stardom_id' => $stardom_details->id] )  }}" onclick="return confirm(&quot;{{ $stardom_details->name }} - {{ tr('stardom_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                </a> 

                                                @else

                                                    <a class="dropdown-item" href="{{ route('admin.stardoms.status' , ['stardom_id' => $stardom_details->id] ) }}">&nbsp;{{ tr('approve') }}</a> 

                                                @endif

                                            </div>

                                        </div>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $stardoms->appends(request()->input())->links() }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection