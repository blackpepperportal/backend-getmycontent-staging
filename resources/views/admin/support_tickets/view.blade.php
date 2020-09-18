@extends('layouts.admin')

@section('title', tr('view_support_tickets'))

@section('content-header', tr('support_tickets'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{tr('home')}}</a>
    </li>

    <li class="breadcrumb-item"><a href="{{route('admin.support_tickets.index')}}">{{tr('support_tickets')}}</a>
    </li>

    <li class="breadcrumb-item active">{{tr('view_support_tickets')}}</a>
    </li>

@endsection

@section('content')

<div class="content-body">

    <div class="col-12">

        <div class="card">

            <div class="card-header border-bottom border-gray">

                <h4 class="card-title">{{ tr('view_support_tickets') }}</h4>
                <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                
            </div>

            <div class="card-body">

              	<div class="card-content">

                    <div class="table-responsive">

                        <table class="table table-xl mb-0">
                            <tr>
                                <th>{{tr('unique_id')}}</th>
                                <td>{{$support_ticket_details->unique_id}}</td>
                            </tr>

                            <tr>
                                <th>{{tr('user_name')}}</th>
                                <td>{{$support_ticket_details->userDetails->name ?? "-"}}</td>
                            </tr>

                            <tr>
                                <th>{{tr('subject')}}</th>
                                <td>{{$support_ticket_details->subject}}</td>
                            </tr>

                            <tr>
                                <th>{{tr('message')}}</th>
                                <td>{{$support_ticket_details->message}}</td>
                            </tr>

                            <tr>
                                <th>{{tr('status')}}</th>
                                <td>
                                    @if($support_ticket_details->status == APPROVED) 

                                        <span class="badge badge-success">{{tr('approved')}}</span>

                                    @else
                                        <span class="badge badge-danger">{{tr('declined')}}</span>

                                    @endif
                                </td>
                            </tr>

                            <tr>
                              <th>{{tr('created_at')}} </th>
                              <td>{{common_date($support_ticket_details->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                            </tr>

                            <tr>
                              <th>{{tr('updated_at')}} </th>
                              <td>{{common_date($support_ticket_details->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
                            </tr> 

                            <tr>
                                <th>{{tr('description')}}</th>
                                <td>{{$support_ticket_details->description}}</td>
                            </tr>  
                            
                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
  
    
@endsection

