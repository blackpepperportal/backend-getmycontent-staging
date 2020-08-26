@extends('layouts.admin') 

@section('title', tr('stardom_withdrawals')) 

@section('content-header', tr('stardom_withdrawals')) 

@section('breadcrumb')

<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ tr('home') }}</a>
</li>

<li class="breadcrumb-item"><a href="{{route('admin.stardom.withdrawals')}}">{{ tr('stardom_withdrawals') }}</a></a>
</li>

<li class="breadcrumb-item">{{ tr('view_stardom_withdrawals') }}</a>
</li>

@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-lg-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_stardom_withdrawals') }}</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        @include('admin.stardom_withdrawals._search')

                        <table class="table table-striped table-bordered sourced-data">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('payment_id') }}</th>
                                    <th>{{ tr('stardom') }}</th>
                                    <th>{{ tr('requested_amount') }}</th>
                                    <th>{{ tr('paid_amount') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('action')}}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($stardom_withdrawals as $i => $stardom_withdrawal_details)
                                <tr>
                                    <td>{{ $i+$stardom_withdrawals->firstItem() }}</td>

                                    <td>{{ $stardom_withdrawal_details->payment_id}}</td>

                                    <td>
                                        <a href="{{  route('admin.stardoms.view' , ['stardom_id' => $stardom_withdrawal_details->stardom_id] )  }}">
                                        {{ $stardom_withdrawal_details->stardomDetails->name ?? "-" }}
                                        </a>
                                    </td>

                                    <td>{{ $stardom_withdrawal_details->requested_amount_formatted }}</td>

                                    <td>
                                        {{ $stardom_withdrawal_details->paid_amount_formatted}}
                                    </td>

                                    <td>
                                        @if($stardom_withdrawal_details->status == PAID)

                                            <span class="badge badge-success">{{tr('paid')}}</span>

                                        @elseif($stardom_withdrawal_details->status == UNPAID)

                                            <span class="badge badge-warning">{{tr('not_paid')}}</span>

                                        @else

                                            <span class="badge badge-danger">{{tr('rejected')}}</span>

                                        @endif
                                    </td>

                                    <td>
                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                @if($stardom_withdrawal_details->paid_amount != $stardom_withdrawal_details->requested_amount && $stardom_withdrawal_details->status == UNPAID)

                                                    <a type="button" class="dropdown-item" data-toggle="modal" data-target="#StardomWithdrawalModel{{$i}}">{{tr('paynow')}}</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a href="{{route('admin.stardom_withdrawals.reject',['stardom_withdrawal_id'=>$stardom_withdrawal_details->id])}}" class="dropdown-item">{{tr('reject')}}</a>
                                                    
                                                @endif

                                            </div>

                                        </div>
                                       
                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $stardom_withdrawals->appends(request()->input())->links() }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@foreach($stardom_withdrawals as $i => $stardom_withdrawal_details)

    @if($stardom_withdrawal_details->requested_amount)

        <div id="StardomWithdrawalModel{{$i}}" class="modal fade" role="dialog">

            <div class="modal-dialog">

                <div class="modal-content">
            
                    <div class="modal-header">
                        
                        <h4 class="modal-title pull-left"><span>{{tr('stardom_is')}} - </span>
                            <a href="{{ route('admin.stardoms.view',['stardom_id' => $stardom_withdrawal_details->stardom_id])}}">{{$stardom_withdrawal_details->stardomDetails->name ?? '-'}}</a> 
                        </h4>

                        <button type="button" class="close" data-dismiss="modal">&times;</button>

                    </div>

                    <div class="modal-body">
                       
                        <div class="row">

                            <div class="col-sm">
                                <b>{{tr('total_paid_amount')}}</b>
                                <p>{{ $stardom_withdrawal_details->requested_amount - $stardom_withdrawal_details->paid_amount}}</p>
                            </div>

                        </div>

                    </div>
                    <div class="modal-footer">
                
                            <form class="forms-sample" action="{{route('admin.stardom_withdrawals.payment')}}" method="POST" enctype="multipart/form-data" role="form" >
                                @csrf

                                <input type="hidden" name="stardom_withdrawal_id" id="stardom_withdrawal_id" value="{{$stardom_withdrawal_details->id}}">

                                <input type="hidden" class="form-control" id="amount" name="amount" placeholder="{{ tr('amount') }}" value="{{ $stardom_withdrawal_details->requested_amount - $stardom_withdrawal_details->paid_amount}}" required>

                                <button type="submit" class="btn btn-info" onclick="return confirm(&quot;{{tr('stardom_payment_confirmation')}}&quot;);" >{{tr('paynow')}}</button>
                            </form>
                        
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{tr('close')}}</button>
                    </div>
                </div>

            </div>
        </div> 
    @endif

@endforeach 


@endsection