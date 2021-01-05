@extends('layouts.admin')

@section('content-header', tr('tip_payments'))

@section('breadcrumb')


<li class="breadcrumb-item">
    <a href="{{ route('admin.users_subscriptions.index') }}">{{ tr('tip_payments') }}</a>
</li>

<li class="breadcrumb-item active" aria-current="page">
    <span>{{ tr('view_tip_payments') }}</span>
</li>

@endsection

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_tip_payments') }}</h4>
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
                                            <td class="text-uppercase">{{ $user_tip->unique_id}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('from_username')}} </td>
                                            <td>
                                                <a href="{{route('admin.users.view',['user_id'=>$user_subscription_payment->from_user_id ?? ''])}}">
                                                    {{ $user_tip->from_username ?: tr('not_available')}}
                                                </a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('to_username')}} </td>
                                            <td>
                                                <a href="{{route('admin.users.view',['user_id'=>$user_subscription_payment->to_user_id ?? ''])}}">
                                                    {{ $user_tip->to_username ?: tr('not_available')}}
                                                </a>

                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('post')}} </td>
                                            <td>
                                                <a href="{{route('admin.posts.view',['post_id'=>$user_tip->post->id ?? ''])}}">
                                                    {{ $user_tip->post->unique_id ?? tr('not_available')}}
                                                </a>

                                            </td>
                                        </tr>


                                        <tr>
                                            <td>{{ tr('payment_id')}} </td>
                                            <td>{{ $user_tip->payment_id}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('amount')}} </td>
                                            <td>{{ $user_tip->amount_formatted}}</td>
                                        </tr>


                                        <tr>
                                            <td>{{ tr('payment_mode')}} </td>
                                            <td>{{ $user_tip->payment_mode}}</td>
                                        </tr>


                                    </tbody>

                                </table>

                            </div>

                            <div class="col-md-6">

                                <table class="table table-bordered table-striped tab-content">

                                    <tbody>



                                        <tr>
                                            <td>{{ tr('paid_date') }}</td>
                                            <td>{{common_date($user_tip->paid_date , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('status') }}</td>
                                            <td>
                                                @if($user_tip->status ==YES)

                                                <span class="badge bg-success">{{tr('yes')}}</span>

                                                @else
                                                <span class="badge bg-danger">{{tr('no')}}</span>

                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('is_cancelled') }}</td>
                                            <td>
                                                @if($user_tip->is_cancelled ==YES)

                                                <span class="badge bg-success">{{tr('yes')}}</span>

                                                @else
                                                <span class="badge bg-danger">{{tr('no')}}</span>

                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('cancel_reason') }}</td>
                                            <td>{{ $user_tip->cancel_reason ?: tr('not_available')}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('created_at') }}</td>
                                            <td>{{common_date($user_tip->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('updated_at') }}</td>
                                            <td>{{common_date($user_tip->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
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