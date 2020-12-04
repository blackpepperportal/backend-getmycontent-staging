@extends('layouts.admin')

@section('content-header', tr('users_subscription_payment'))

@section('breadcrumb')


<li class="breadcrumb-item">
    <a href="{{ route('admin.users_subscriptions.index') }}">{{ tr('users_subscription_payment') }}</a>
</li>

<li class="breadcrumb-item active" aria-current="page">
    <span>{{ tr('view_user_subscription_payment') }}</span>
</li>

@endsection

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_user_subscription_payment') }}</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-6">

                                <table class="table table-bordered table-striped tab-content">

                                    <tbody>

                                        <tr>
                                            <td>{{ tr('unique_id')}} </td>
                                            <td class="text-uppercase">{{ $user_subscription_payment->unique_id}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('from_username')}} </td>
                                            <td>
                                                <a href="{{route('admin.users.view',['user_id'=>$user_subscription_payment->from_user_id ?? ''])}}">
                                                    {{ $user_subscription_payment->from_username ?: tr('not_available')}}
                                                </a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('to_username')}} </td>
                                            <td>
                                                <a href="{{route('admin.users.view',['user_id'=>$user_subscription_payment->to_user_id ?? ''])}}">
                                                    {{ $user_subscription_payment->to_username ?: tr('not_available')}}
                                                </a>

                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('payment_id')}} </td>
                                            <td>{{ $user_subscription_payment->payment_id}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('plan')}} </td>
                                            <td>{{ $user_subscription_payment->plan_text_formatted ?? ''}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('amount')}} </td>
                                            <td>{{ $user_subscription_payment->amount_formatted}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('admin_amount')}} </td>
                                            <td>{{ $user_subscription_payment->admin_amount_formatted}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('user_amount')}} </td>
                                            <td>{{ $user_subscription_payment->user_amount_formatted}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('payment_mode')}} </td>
                                            <td>{{ $user_subscription_payment->payment_mode}}</td>
                                        </tr>


                                        <tr>
                                            <td>{{ tr('plan_type')}} </td>
                                            <td>{{ $user_subscription_payment->plan_type}}</td>
                                        </tr>

                                    </tbody>

                                </table>

                            </div>

                            <div class="col-md-6">

                                <table class="table table-bordered table-striped tab-content">

                                    <tbody>

                                        <tr>
                                            <td>{{ tr('expiry_date') }}</td>
                                            <td>{{common_date($user_subscription_payment->expiry_date , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('paid_date') }}</td>
                                            <td>{{common_date($user_subscription_payment->paid_date , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('status') }}</td>
                                            <td>
                                                @if($user_subscription_payment->status ==YES)

                                                <span class="badge bg-success">{{tr('yes')}}</span>

                                                @else
                                                <span class="badge bg-danger">{{tr('no')}}</span>

                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('is_cancelled') }}</td>
                                            <td>
                                                @if($user_subscription_payment->is_cancelled ==YES)

                                                <span class="badge bg-success">{{tr('yes')}}</span>

                                                @else
                                                <span class="badge bg-danger">{{tr('no')}}</span>

                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('cancel_reason') }}</td>
                                            <td>{{ $user_subscription_payment->cancel_reason ?: tr('not_available')}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('created_at') }}</td>
                                            <td>{{common_date($user_subscription_payment->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('updated_at') }}</td>
                                            <td>{{common_date($user_subscription_payment->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
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

</section>

@endsection