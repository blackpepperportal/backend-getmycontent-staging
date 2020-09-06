@extends('layouts.admin') 

@section('title', tr('user_withdrawals')) 

@section('content-header', tr('user_withdrawals')) 

@section('breadcrumb')

<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ tr('home') }}</a>
</li>

<li class="breadcrumb-item"><a href="{{route('admin.user_withdrawals')}}">{{ tr('user_withdrawals') }}</a></a>
</li>

<li class="breadcrumb-item">{{ tr('view_user_withdrawals') }}</a>
</li>

@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-lg-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_user_withdrawals') }}</h4>
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

                                @foreach($user_withdrawals as $i => $user_withdrawal_details)
                                <tr>
                                    <td>{{ $i+$user_withdrawals->firstItem() }}</td>

                                    <td>{{ $user_withdrawal_details->payment_id}}</td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $user_withdrawal_details->user_id] )  }}">
                                        {{ $user_withdrawal_details->userDetails->name ?? "-" }}
                                        </a>
                                    </td>

                                    <td>{{ $user_withdrawal_details->requested_amount_formatted }}</td>

                                    <td>
                                        {{ $user_withdrawal_details->paid_amount_formatted}}
                                    </td>

                                    <td>
                                        @if($user_withdrawal_details->status == WITHDRAW_PAID)

                                            <span class="badge badge-success">{{tr('paid')}}</span>

                                        @elseif($user_withdrawal_details->status == WITHDRAW_INITIATED)

                                            <span class="badge badge-warning">{{tr('initiated')}}</span>

                                        @else

                                            <span class="badge badge-danger">{{tr('rejected')}}</span>

                                        @endif
                                    </td>

                                    <td>
                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                @if(in_array($user_withdrawal_details->status,[ WITHDRAW_INITIATED,WITHDRAW_ONHOLD]))

                                                    <a type="button" class="dropdown-item" href="{{route('admin.user_withdrawals.paynow',['user_withdrawal_id'=>$user_withdrawal_details->id])}}" onclick="return confirm('Do you want to pay?')">{{tr('paynow')}}</a>
                                                    <div class="dropdown-divider"></div>
                                                    
                                                    <a href="{{route('admin.user_withdrawals.reject',['user_withdrawal_id'=>$user_withdrawal_details->id])}}" class="dropdown-item">{{tr('reject')}}</a>
                                                    
                                                @endif

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

@endsection