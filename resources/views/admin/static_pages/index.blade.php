@extends('layouts.admin') 

@section('content-header', tr('static_pages'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{tr('home')}}</a>
    </li>

    <li class="breadcrumb-item"><a href="{{ route('admin.static_pages.index' )}}">{{tr('static_pages')}}</a></li>

    <li class="breadcrumb-item active" aria-current="page">
        <span>{{ tr('view_static_pages') }}</span>
    </li>
           
@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_static_pages') }}</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                    <div class="heading-elements">
                        <a href="{{ route('admin.static_pages.create') }}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('add_static_page') }}</a>
                    </div>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        <table class="table table-striped table-bordered sourced-data" id="myTable">
                            <thead>
                                <tr>
                                    <th>{{tr('s_no')}}</th>
                                    <th>{{tr('title')}}</th>
                                    <th>{{tr('static_page_type')}}</th>
                                    <th>{{tr('status')}}</th>
                                    <th>{{tr('action')}}</th>
                                </tr>
                            </thead>

                            <tbody>


                                @foreach($static_pages as $i => $static_page_details)

                                    <tr>
                                        <td>{{$i+$static_pages->firstItem()}}</td>

                                        <td>
                                            <a href="{{route('admin.static_pages.view' , ['static_page_id'=> $static_page_details->id] )}}"> {{$static_page_details->title}}</a>
                                        </td>

                                        <td class="text-capitalize">{{$static_page_details->type}}</td>

                                        <td>
                                            @if($static_page_details->status == APPROVED)

                                              <span class="badge badge-success">{{tr('approved')}}</span> 

                                            @else

                                              <span class="badge badge-warning">{{tr('pending')}}</span> 
                                            @endif
                                        </td>

                                        <td>  

                                            <div class="btn-group" role="group">

                                                <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuOutlineButton1">

                                                    <a class="dropdown-item" href="{{ route('admin.static_pages.view', ['static_page_id' => $static_page_details->id] ) }}">
                                                        {{tr('view')}}
                                                    </a>

                                                    @if(Setting::get('is_demo_control_enabled') == NO)
                                                    
                                                        <a class="dropdown-item" href="{{ route('admin.static_pages.edit', ['static_page_id' => $static_page_details->id] ) }}">
                                                            {{tr('edit')}}
                                                        </a>

                                                        <a class="dropdown-item" 
                                                        onclick="return confirm(&quot;{{tr('static_page_delete_confirmation' , $static_page_details->title)}}&quot;);" href="{{ route('admin.static_pages.delete', ['static_page_id' => $static_page_details->id] ) }}" >
                                                            {{ tr('delete') }}
                                                        </a>

                                                    @else

                                                        <a class="dropdown-item text-muted" href="javascript:;">{{tr('edit')}}</a>

                                                        <a class="dropdown-item text-muted" href="javascript:;">{{ tr('delete') }}</a>

                                                    @endif                  

                                                    <div class="dropdown-divider"></div>

                                                    @if($static_page_details->status == APPROVED)

                                                        <a class="dropdown-item" href="{{ route('admin.static_pages.status', ['static_page_id' =>  $static_page_details->id] ) }}" 
                                                        onclick="return confirm(&quot;{{$static_page_details->title}} - {{tr('static_page_decline_confirmation')}}&quot;);"> 
                                                            {{tr('decline')}}
                                                        </a>

                                                    @else

                                                        <a class="dropdown-item" href="{{ route('admin.static_pages.status', ['static_page_id' =>  $static_page_details->id] ) }}">
                                                            {{tr('approve')}}
                                                        </a>
                                                           
                                                    @endif

                                                </div>
                                                 
                                            </div>
                                        

                                        </td>
                                    
                                    </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection