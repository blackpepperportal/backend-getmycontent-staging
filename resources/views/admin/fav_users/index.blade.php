@extends('layouts.admin') 

@section('title', tr('favorite_users')) 

@section('content-header', tr('favorite_users')) 

@section('breadcrumb')

<li class="breadcrumb-item active">
    <a href="{{route('admin.users.index')}}">{{ tr('users') }}</a>
</li>
    
<li class="breadcrumb-item">{{ tr('view_favourite_users') }}</li>

@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_favourite_users') }} - <a href="{{ route('admin.users.view',['user_id'=>$user->id ?? '']) }}">{{$user->name}}</a>
                    
                    </h4>

                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        @include('admin.fav_users._search')
                        
                        <table class="table table-striped table-bordered sourced-data">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('username') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($fav_users as $i => $fav_user)

                                <tr>
                                    <td>{{ $i + $fav_users->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $fav_user->fav_user_id] )  }}">
                                        {{ $fav_user->fav_username ?? "-" }}
                                        </a>
                                    </td>

                                   
                                    <td>
                                    
                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-success dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                
                                                  <a class="dropdown-item" href="{{ route('admin.users.view', ['user_id' => $fav_user->user_id] ) }}">&nbsp;{{ tr('view') }}</a>


                                                @if(Setting::get('is_demo_control_enabled') == YES)

                                        
                                                    <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a> 

                                                @else

                                                    <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('fav_user_delete_confirmation' , $fav_user->fav_user_id) }}&quot;);" href="{{ route('admin.fav_users.delete', ['fav_user_id' => $fav_user->fav_user_id,'user_id' => $fav_user->user_id] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                @endif

                                            </div>

                                        </div>


                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $fav_users->appends(request()->input())->links() }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection