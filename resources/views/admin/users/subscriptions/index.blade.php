@extends('layouts.admin') 

@section('content-header', tr('users_subscription_payment'))

@section('breadcrumb')

    <li class="breadcrumb-item active" aria-current="page">
        <span>{{ tr('users_subscription_payment') }}</span>
    </li> 
           
@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_user_subscriptions_payment') }}</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        <table class="table table-striped table-bordered sourced-data">

                            <thead>
                                <tr>
                                    <th>{{tr('s_no')}}</th>
                                    <th>{{tr('from_username')}}</th>
                                    <th>{{tr('to_username')}}</th>
                                    <th>{{tr('plan')}}</th>
                                    <th>{{tr('amount')}}</th>
                                    <th>{{tr('status')}}</th>
                                    <th>{{tr('action')}}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($user_subscriptions as $i => $subscription)
                                      
                                    <tr>
                                        <td>{{$i+$user_subscriptions->firstItem()}}</td>

                                        <td>
                                            <a href="{{route('admin.users.view' , ['user_id' => $subscription->from_user_id])}}"> {{ $subscription->from_username }}
                                            </a>
                                        </td>

                                        <td><a href="{{route('admin.users.view' , ['user_id' => $subscription->to_user_id])}}"> {{ $subscription->to_username }}</a></td>

                                        <td>{{ $subscription->plan }}</td>

                                        <td>{{ $subscription->amount_formatted }}</td>

                                        <td>

                                            @if($subscription->status == APPROVED)

                                                <span class="badge bg-success">{{ tr('approved') }} </span>

                                            @else

                                                <span class="badge bg-danger">{{ tr('declined') }} </span>

                                            @endif

                                        </td>
                                            
                                        <td>     

                                            <div class="template-demo">

                                                <div class="dropdown">

                                                   <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>


                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuOutlineButton1">
                                                      
                                                        <a class="dropdown-item" href="{{ route('admin.user_subscriptions.view', ['subscription_id' => $subscription->id]) }}">
                                                            {{tr('view')}}
                                                        </a>


                                                    </div>

                                                </div>

                                            </div>

                                        </td>

                                    </tr>

                                @endforeach
                                
                            </tbody>
                        
                        </table>
                        <div class="pull-right" id="paglink">{{ $user_subscriptions->appends(request()->input())->links() }}</div>
                        

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>
@endsection
