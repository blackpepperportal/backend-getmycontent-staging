@extends('layouts.admin') 

@section('title', tr('payments')) 

@section('content-header', tr('payments')) 

@section('breadcrumb')

<li class="breadcrumb-item active">
    <a href="">{{ tr('payments') }}</a>
</li>
<li class="breadcrumb-item">{{tr('order_payments')}}</li>

@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('order_payments') }}</h4>

                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        @include('admin.orders._payment_search')
                        
                        <table class="table table-striped table-bordered sourced-data">
                                    
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('order_id')}}</th>
                                    <th>{{ tr('payment_id') }}</th>
                                    <th>{{ tr('user') }}</th>
                                    <th>{{ tr('delivery_price') }}</th>
                                    <th>{{ tr('sub_total') }}</th>
                                    <th>{{ tr('total') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($order_payments as $i => $order_payment)

                                    <tr>
                                        <td>{{ $i + 1 }}</td>

                                        <td><a href="{{route('admin.orders.view',['order_id' => $order_payment->order_id])}}">{{$order_payment->unique_id}}</a></td>

                                        <td>
                                            {{ $order_payment->payment_id }}
                                        </td>

                                        <td>
                                            <a href="{{route('admin.users.view',['user_id' => $order_payment->user_id])}}">{{$order_payment->user->name ?? "-"}}</a>
                                        </td>

                                        <td>
                                            {{ $order_payment->delivery_price_formatted}}
                                        </td>

                                        <td>{{$order_payment->sub_total_formatted}}</td>

                                        <td>{{$order_payment->total_formatted}}</td>

                                        <td><a class="btn btn-primary" href="{{route('admin.order.payments.view',['order_payment_id' => $order_payment->id])}}">{{tr('view')}}</a></td>

                                    </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $order_payments->appends(request()->input())->links() }}</div>



                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection