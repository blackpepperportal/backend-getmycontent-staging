@extends('layouts.admin')

@section('title', tr('view_user_withdrawals'))

@section('content-header', tr('user_withdrawals'))

@section('breadcrumb')

    

    <li class="breadcrumb-item"><a href="{{route('admin.user_withdrawals')}}">{{tr('user_withdrawals')}}</a>
    </li>

    <li class="breadcrumb-item active">{{tr('view_user_withdrawals')}}</a>
    </li>

@endsection

@section('content')

<div class="content-body">
    <div class="row">
        <div class="col-md-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_user_withdrawals') }} - <a href="{{route('admin.users.view',['user_id' => $user_withdrawal->user_id])}}">{{$user_withdrawal->user->name ?? "-"}}</a>	</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                    
                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-lg-6">

                            <table class="table table-bordered table-striped tab-content table-responsive-sm">
                
                                <tbody>

                                    <tr>
                                        <td>{{ tr('unique_id') }}</td>
                                        <td>{{$user_withdrawal->unique_id}}</td>
                                    </tr>

                                    <tr>
                                        <td>{{ tr('user') }}</td>
                                        <td><a href="{{route('admin.users.view',['user_id'=>$user_withdrawal->user_id])}}">{{$user_withdrawal->user->name ?? "-"}}</a></td>
                                    </tr>

                                    <tr>
                                        <td>{{ tr('payment_id') }}</td>
                                        <td>{{$user_withdrawal->payment_id}}</td>
                                    </tr>

                                    <tr>
                                        <td>{{ tr('paid_amount') }}</td>
                                        <td>{{$user_withdrawal->paid_amount_formatted}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{tr('email')}}</td>
                                        <td>{{$user_withdrawal->user->email ?? "-"}}</td>
                                    </tr>

                                    <tr>
                                        <td>{{tr('payment_mode')}}</td>
                                        <td><span class="badge badge-secondary">{{$user_withdrawal->payment_mode}}</span></td>
                                    </tr>

                                    <tr>
                                        <td>{{ tr('status') }}</td>
                                        <td>
                                            @if($user_withdrawal->status ==YES)

                                                <span class="badge bg-success">{{tr('yes')}}</span>

                                            @else 
                                                <span class="badge bg-danger">{{tr('no')}}</span>

                                            @endif
                                        </td>
                                    </tr>

                                </tbody>

                            </table>
                        </div>

                        <div class="col-lg-6">

                            <table class="table table-bordered table-striped tab-content table-responsive-sm">
                        
                                <tbody>
                                    <tr>
                                        <td>{{tr('account_holder_name')}}</td>
                                        <td>{{$billing_account_details->account_holder_name ?? "-"}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{tr('account_no')}}</td>
                                        <td>{{$billing_account_details->account_number ?? "-"}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{tr('ifsc_code')}}</td>
                                        <td>{{$billing_account_details->ifsc_code ?? "-"}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ tr('bank_name') }}</td>
                                        <td>{{$billing_account_details->bank_name ?? "-"}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{tr('swift_code')}}</td>
                                        <td>{{$billing_account_details->swift_code ?? "-"}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ tr('amount') }}</td>
                                        <td>{{$user_withdrawal->requested_amount_formatted}}</td>
                                    </tr>

                                    <tr>
                                        <td>{{ tr('updated_at') }}</td>
                                        <td>{{common_date($user_withdrawal->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
                                    </tr>

                                    <tr>
                                        <td>{{ tr('created_at') }}</td>
                                        <td>{{common_date($user_withdrawal->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                    </tr>

                                </tbody>

                            </table>
                            
                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>

</div>
  
    
@endsection

