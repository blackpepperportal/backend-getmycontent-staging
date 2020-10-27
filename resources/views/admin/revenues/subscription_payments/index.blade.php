@extends('layouts.admin') 

@section('title', tr('subscription_payments')) 

@section('content-header', tr('subscription_payments')) 

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{tr('home')}}</a>
    </li>
<li class="breadcrumb-item active">
    <a href="{{route('admin.subscription_payments.index')}}">{{ tr('subscription_payments') }}</a>
</li>

<li class="breadcrumb-item active">{{ tr('view_subscription_payments') }}</li>

@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">

                        {{ tr('view_subscription_payments') }}

                    </h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        @include('admin.revenues.subscription_payments._search')

                        <table class="table table-striped table-bordered sourced-data">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('payment_id')}}</th>
                                    <th>{{ tr('user') }}</th>
                                    <th>{{ tr('amount') }}</th>
                                    <th>{{ tr('expiry_date') }}</th>
                                    
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($subscription_payments as $i => $subscription_payment_details)
                                <tr>
                                    <td>{{ $i+$subscription_payments->firstItem() }}</td>

                                    <td> <a href="{{ route('admin.subscription_payments.view', ['subscription_payment_id' => $subscription_payment_details->id] ) }}">{{$subscription_payment_details->payment_id}}</a></td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $subscription_payment_details->user_id] )  }}">
                                        {{ $subscription_payment_details->user->name ?? "-" }}
                                        </a>
                                    </td>

                                    <td>
                                        {{ $subscription_payment_details->amount_formatted}}
                                    </td>

                                    <td><span class="text-danger">{{common_date($subscription_payment_details->expiry_time , Auth::guard('admin')->user()->timezone)}}</span></td>

                                    <td>
                                        @if($subscription_payment_details->status == APPROVED)

                                            <span class="btn btn-success btn-sm">{{ tr('approved') }}</span>
                                        @else

                                            <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span>
                                        @endif
                                    </td>


                                    <td>

                                        <a class="btn btn-primary" href="{{ route('admin.subscription_payments.view', ['subscription_payment_id' => $subscription_payment_details->id] ) }}">&nbsp;{{ tr('view') }}</a> 
                                    
                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $subscription_payments->appends(request()->input())->links() }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection