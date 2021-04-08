@extends('layouts.admin')

@section('title', tr('view_user_wallets'))

@section('content-header', tr('user_wallets'))

@section('breadcrumb')



<li class="breadcrumb-item"><a href="{{route('admin.user_wallets.index')}}">{{tr('user_wallets')}}</a>
</li>

<li class="breadcrumb-item active">{{tr('view_user_wallets')}}</a>
</li>

@endsection

@section('content')

<div class="content-body">

    <div class="card">

        <div class="card-header border-bottom border-gray">

            <h4 class="card-title">{{ tr('view_user_wallets') }} - <a
                    href="{{route('admin.users.view',['user_id' => $user_wallet->user_id])}}">{{$user_wallet->user->name ?? "-"}}</a>
            </h4>
            <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card bg-warning">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body white text-left">
                                        <h3>{{$user_wallet->total_formatted}}</h3>
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

                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card bg-success">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body white text-left">
                                        <h3>{{$user_wallet->used_formatted}}</h3>
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

                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card bg-info">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body white text-left">
                                        <h3>{{$user_wallet->onhold_formatted}}</h3>
                                        <span>{{tr('onhold')}}</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="icon-support white font-large-2 float-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card bg-danger">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body white text-left">
                                        <h3>{{$user_wallet->remaining_formatted}}</h3>
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

            <div class="row">

				<div class="col-md-12">
					<div class="card-title">{{tr('payment_history')}}</div>

					<table class="table table-striped table-bordered table-responsive">

						<thead>
							<tr>
								<th>{{ tr('s_no') }}</th>
								<th>{{ tr('payment_id') }} </th>
								<th>{{ tr('payment_mode') }}</th>
								<th>{{ tr('requested_amount') }}</th>
                                <th>{{ tr('message') }}</th>
								<th>{{ tr('status') }}</th>
							</tr>
						</thead>

						<tbody>

							@if($user_wallet_payments->isNotEmpty())

    							@foreach($user_wallet_payments as $i => $user_wallet_payment)

        							<tr>
        								<td>{{ $i+$user_wallet_payments->firstItem() }}</td>

        								<td>
                                            {{ $user_wallet_payment->payment_id}}
                                            <br>
                                            <br>
                                            <span class="text-gray">{{tr('date')}}: {{common_date($user_wallet_payment->paid_date, Auth::user()->timezone)}}</span>
                                        </td>

        								<td>{{ $user_wallet_payment->payment_mode }}</td>

        								<td>{{ $user_wallet_payment->requested_amount_formatted }}
                                            <br><br>
                                            <span class="text-gray"> Admin: {{$user_wallet_payment->admin_amount_formatted}}</span>
                                            <span class="text-gray"> User: {{$user_wallet_payment->user_amount_formatted}}</span>
                                        </td>

                                        <td>{{ $user_wallet_payment->message }}</td>

        								<td>
        									@if($user_wallet_payment->status == PAID)

        									<span class="btn btn-success btn-sm">{{ tr('paid') }}</span>
        									@else

        									<span class="btn btn-warning btn-sm">{{ tr('not_paid') }}</span>
        									@endif
        								</td>

        							</tr>

    							@endforeach

							@else

    							<tr colspan="8" class="text-center">
    								<td>
    									<h4>{{tr('no_results_found')}}</h4>
    								</td>
    							</tr>

							@endif

						</tbody>

					</table>

					<div class="pull-right" id="paglink">{{ $user_wallet_payments->appends(request()->input())->links() }}
					</div>
				</div>

            </div>

        </div>

    </div>

</div>

@endsection