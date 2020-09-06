@extends('layouts.admin')

@section('title', tr('view_user_wallets'))

@section('content-header', tr('user_wallets'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{tr('home')}}</a>
    </li>

    <li class="breadcrumb-item"><a href="{{route('admin.user_wallets.index')}}">{{tr('user_wallets')}}</a>
    </li>

    <li class="breadcrumb-item active">{{tr('view_user_wallets')}}</a>
    </li>

@endsection

@section('content')

<div class="content-body">

    <div class="col-12">

        <div class="card">

            <div class="card-header border-bottom border-gray">

                <h4 class="card-title">{{ tr('view_user_wallets') }} - <a href="{{route('admin.users.view',['user_id' => $user_wallet_details->user_id])}}">{{$user_wallet_details->userDetails->name ?? "-"}}</a>	</h4>
                <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                
            </div>

            <div class="card-body">

                <div class="row">

				    <div class="col-xl-4 col-lg-6 col-12">
				        <div class="card bg-warning">
				            <div class="card-content">
				                <div class="card-body">
				                    <div class="media d-flex">
				                        <div class="media-body white text-left">
				                            <h3>{{$user_wallet_details->total_formatted}}</h3>
				                            <span>{{tr('total')}}</span>
				                        </div>
				                        <div class="align-self-center">
				                            <i class="icon-wallet font-large-2 white"></i>
				                        </div>
				                    </div>
				                </div>
				            </div>
				        </div>
				    </div>

				    <div class="col-xl-4 col-lg-6 col-12">
				        <div class="card bg-success">
				            <div class="card-content">
				                <div class="card-body">
				                    <div class="media d-flex">
				                        <div class="media-body white text-left">
				                            <h3>{{$user_wallet_details->used_formatted}}</h3>
				                            <span>{{tr('used')}}</span>
				                        </div>
				                        <div class="align-self-center">
				                            <i class="icon-support white font-large-2 float-right"></i>
				                        </div>
				                    </div>
				                </div>
				            </div>
				        </div>
				    </div>

				    <div class="col-xl-4 col-lg-6 col-12">
				        <div class="card bg-danger">
				            <div class="card-content">
				                <div class="card-body">
				                    <div class="media d-flex">
				                        <div class="media-body white text-left">
				                            <h3>{{$user_wallet_details->remaining_formatted}}</h3>
				                            <span>{{tr('remaining')}}</span>
				                        </div>
				                        <div class="align-self-center">
				                            <i class="icon-pie-chart white font-large-2 float-right"></i>
				                        </div>
				                    </div>
				                </div>
				            </div>
				        </div>
				    </div>

				</div>

				<div class="card-title">{{tr('payment_history')}}</div>

				<table class="table table-striped table-bordered">
                            
                    <thead>
                        <tr>
                            <th>{{ tr('s_no') }}</th>
                            <th>{{ tr('payment_id') }} </th> 
                            <th>{{ tr('payment_mode') }}</th>
                            <th>{{ tr('requested_amount') }}</th>
                            <th>{{ tr('paid_date') }}</th>
                            <th>{{ tr('status') }}</th>
                        </tr>
                    </thead>
                   
                    <tbody>

                        @foreach($user_wallet_payments as $i => $stardom_wallet_payment_details)
                        <tr>
                            <td>{{ $i+$user_wallet_payments->firstItem() }}</td>

                            <td>{{ $stardom_wallet_payment_details->payment_id}}</td>

                            <td>{{ $stardom_wallet_payment_details->payment_mode }}</td>

                            <td>{{ $stardom_wallet_payment_details->requested_amount_formatted }}</td>

                            <td>
                                {{common_date($stardom_wallet_payment_details->paid_date,Auth::guard('admin')->user()->timezone)}}
                            </td>

                            <td>
                                @if($stardom_wallet_payment_details->status == PAID)

                                	<span class="btn btn-success btn-sm">{{ tr('paid') }}</span> 
                                @else

                                	<span class="btn btn-warning btn-sm">{{ tr('not_paid') }}</span> 
                                @endif
                            </td>

                        </tr>

                        @endforeach

                    </tbody>
                
                </table>

                <div class="pull-right" id="paglink">{{ $user_wallet_payments->appends(request()->input())->links() }}</div>

            </div>

        </div>

    </div>

</div>
  
    
@endsection

