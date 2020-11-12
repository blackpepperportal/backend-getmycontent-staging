@extends('layouts.admin')

@section('title', tr('revenue_management'))

@section('content-header', tr('revenue_management'))

@section('breadcrumb')

<li class="breadcrumb-item">{{ tr('user_withdrawals') }}</a></li>

@endsection

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-lg-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('user_withdrawals') }}</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        @include('admin.user_withdrawals._search')

                        <table class="table table-striped table-bordered sourced-data">

                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('payment_id') }}</th>
                                    <th>{{ tr('content_creator') }}</th>
                                    <th>{{ tr('requested_amount') }}</th>
                                    <th>{{ tr('paid_amount') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('action')}}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($user_withdrawals as $i => $user_withdrawal)
                                <tr>
                                    <td>{{ $i+$user_withdrawals->firstItem() }}</td>

                                    <td>{{ $user_withdrawal->payment_id ?: '-'}}</td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $user_withdrawal->user_id] )  }}">
                                            {{ $user_withdrawal->user->name ?? "-" }}
                                        </a>
                                    </td>

                                    <td>{{ $user_withdrawal->requested_amount_formatted }}</td>

                                    <td>
                                        {{ $user_withdrawal->paid_amount_formatted}}
                                    </td>

                                    <td>
                                        @if($user_withdrawal->status == WITHDRAW_PAID)

                                        <span class="badge badge-success">{{tr('paid')}}</span>

                                        @elseif($user_withdrawal->status == WITHDRAW_INITIATED)

                                        <span class="badge badge-warning">{{tr('initiated')}}</span>

                                        @else

                                        <span class="badge badge-danger">{{tr('rejected')}}</span>

                                        @endif
                                    </td>

                                    <td>
                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                @if(in_array($user_withdrawal->status,[ WITHDRAW_INITIATED,WITHDRAW_ONHOLD]))

                                                <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#paynowModal{{$i}}">
                                                    
                                                    <span class="nav-text">{{tr('paynow')}}</span>

                                                </a>

                                                <div class="dropdown-divider"></div>

                                                <a href="{{route('admin.user_withdrawals.reject',['user_withdrawal_id'=>$user_withdrawal->id])}}" class="dropdown-item" onclick="return confirm(&quot;{{tr('user_withdrawal_reject_confirmation')}}&quot;);">{{tr('reject')}}</a>

                                                @endif

                                                <div class="dropdown-divider"></div>


                                                <a href="{{route('admin.user_withdrawals.view',['user_withdrawal_id'=>$user_withdrawal->id])}}" class="dropdown-item">{{tr('view')}}</a>

                                            </div>

                                        </div>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right" id="paglink">{{ $user_withdrawals->appends(request()->input())->links() }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


@foreach($user_withdrawals as $i => $withdrawal_details)

<div id="paynowModal{{$i}}" class="modal fade" role="dialog">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h4 class="modal-title pull-left">
                    <a href="{{route('admin.users.view' , ['user_id' => $withdrawal_details->user_id])}}"> {{ $withdrawal_details->userDetails->name ?? tr('user_details_not_avail')}}
                    </a>
                </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">

                <div class="row">

                    <div class="col-sm popup-label">
                        <b class="label-font">{{tr('account_holder_name')}}</b>
                        <p>{{$withdrawal_details->billingaccountDetails->account_holder_name ?? tr('not_available') }}</p>
                    </div>

                    <div class="col-sm popup-label">
                        <b class="label-font">{{tr('account_number')}}</b>
                        <p>{{$withdrawal_details->billingaccountDetails->account_number ?? tr('not_available') }}</p>
                    </div>

                </div>

                <div class="row">

                    <div class="col-sm popup-label">
                        <b class="label-font">{{tr('bank_name')}}</b>
                        <p>{{$withdrawal_details->billingaccountDetails->bank_name ?? tr('not_available') }}</p>
                    </div>

                    <div class="col-sm popup-label">
                        <b class="label-font">{{tr('ifsc_code')}}</b>
                        <p>{{$withdrawal_details->billingaccountDetails->ifsc_code ?? tr('not_available') }}</p>
                    </div>


                </div>

                <div class="row">

                    <div class="col-sm popup-label">
                        <b class="label-font">{{tr('swift_code')}}</b>
                        <p>{{$withdrawal_details->billingaccountDetails->swift_code ?? tr('not_available') }}</p>
                    </div>

                    <div class="col-sm popup-label">
                        <b class="label-font">{{tr('created_at')}}</b>
                        <p>{{ common_date($withdrawal_details->created_at,Auth::guard('admin')->user()->timezone,'d M Y') }}</p>
                    </div>

                </div>

            </div>


            <div class="row">

                <div class="col-sm popup-label popup-left">
                    <b class="label-font">{{tr('requested_amount')}}</b>
                    <p>{{formatted_amount($withdrawal_details->requested_amount ?? '0.00')}}</p>
                </div>

                <div class="col-sm popup-label"></div>

            </div>

            <div class="modal-footer">

                <form class="forms-sample" action="{{ route('admin.user_withdrawals.paynow', ['user_withdrawal_id' => $withdrawal_details->id]) }}" method="GET" role="form">
                    @csrf

                    <input type="hidden" name="user_withdrawal_id" id="user_withdrawal_id" value="{{$withdrawal_details->id}}">

                    <button type="submit" class="btn btn-info btn-sm" onclick="return confirm(&quot;{{tr('user_withdrawal_paynow_confirmation')}}&quot;);">{{tr('paynow')}}</button>
                </form>

                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">{{tr('close')}}</button>
            </div>
        </div>

    </div>
</div>
@endforeach

@endsection