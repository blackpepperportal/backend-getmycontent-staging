@extends('layouts.admin') 

@section('content-header', tr('subscriptions'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{tr('home')}}</a></li>

    <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions.index')}}">{{tr('subscriptions')}}</a></li>

    <li class="breadcrumb-item active" aria-current="page">
        <span>{{ tr('view_subscriptions') }}</span>
    </li> 
           
@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_subscriptions') }}</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                    <div class="heading-elements">
                        <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('add_subscription') }}</a>
                    </div>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        <table class="table table-striped table-bordered sourced-data">

                            <thead>
                                <tr>
                                    <th>{{tr('s_no')}}</th>
                                    <th>{{tr('title')}}</th>
                                    <th>{{tr('plan')}}</th>
                                    <th>{{tr('status')}}</th>
                                    <th>{{tr('amount')}}</th>
                                    <th>{{tr('action')}}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($subscriptions as $i => $subscription_details)
                                      
                                    <tr>
                                        <td>{{$i+$subscriptions->firstItem()}}</td>

                                        <td>
                                            <a href="{{route('admin.subscriptions.view' , ['subscription_id' => $subscription_details->id])}}"> {{ $subscription_details->title }}
                                            </a>
                                        </td>

                                        <td>{{$subscription_details->plan_type_formatted}}</td>
                                      
                                        <td>

                                            @if($subscription_details->status == APPROVED)

                                                <span class="badge bg-success">{{ tr('approved') }} </span>

                                            @else

                                                <span class="badge bg-danger">{{ tr('declined') }} </span>

                                            @endif

                                        </td>
                                            
                                        <td>  
                                            {{formatted_amount ($subscription_details->amount)}}           
                                        </td>

                                        <td>     

                                            <div class="template-demo">

                                                <div class="dropdown">

                                                    <button class="btn btn-outline-primary  dropdown-toggle" type="button" id="dropdownMenuOutlineButton1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        {{tr('action')}}
                                                    </button>

                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuOutlineButton1">
                                                      
                                                        <a class="dropdown-item" href="{{ route('admin.subscriptions.view', ['subscription_id' => $subscription_details->id]) }}">
                                                            {{tr('view')}}
                                                        </a>
                                                        
                                                        @if(Setting::get('is_demo_control_enabled') == NO)
                                                            <a class="dropdown-item" href="{{ route('admin.subscriptions.edit', ['subscription_id' => $subscription_details->id]) }}">
                                                                {{tr('edit')}}
                                                            </a>
                                                            
                                                            <a class="dropdown-item" href="{{route('admin.subscriptions.delete', ['subscription_id' => $subscription_details->id])}}" 
                                                            onclick="return confirm(&quot;{{tr('subscription_delete_confirmation' , $subscription_details->title)}}&quot;);">
                                                                {{tr('delete')}}
                                                            </a>
                                                        @else

                                                            <a class="dropdown-item text-muted" href="javascript:;" >{{tr('edit')}}</a>
                                                          
                                                            <a class="dropdown-item text-muted" href="javascript:;" onclick="return confirm(&quot;{{tr('subscription_delete_confirmation' , $subscription_details->title)}}&quot;);">{{tr('delete')}}</a>                           
                                                        @endif

                                                        <div class="dropdown-divider"></div>


                                                        @if($subscription_details->status == APPROVED)

                                                            <a class="dropdown-item" href="{{ route('admin.subscriptions.status', ['subscription_id' => $subscription_details->id]) }}" onclick="return confirm(&quot;{{$subscription_details->title}} - {{tr('subscription_decline_confirmation')}}&quot;);" >
                                                                {{ tr('decline') }} 
                                                            </a>

                                                        @else
                                                            
                                                            <a class="dropdown-item" href="{{ route('admin.subscriptions.status', ['subscription_id' => $subscription_details->id]) }}">
                                                                {{ tr('approve') }} 
                                                            </a>
                                                               
                                                        @endif


                                                    </div>

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

