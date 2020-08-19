@extends('layouts.admin') 

@section('title', tr('orders')) 

@section('content-header', tr('orders')) 

@section('breadcrumb')

<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ tr('home') }}</a>
</li>
<li class="breadcrumb-item active"><a href="">{{ tr('orders') }}</a>
</li>
<li class="breadcrumb-item">{{tr('view_orders')}}</li>

@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_orders') }}</h4>

                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                         <form method="GET" action="{{route('admin.orders.index')}}">

                            <div class="row">

                                <div class="col-6"></div>

                                <div class="col-6">

                                    <div class="input-group">
                                       
                                        <input type="text" class="form-control" name="search_key"
                                        placeholder="{{tr('orders_search_placeholder')}}"> <span class="input-group-btn">
                                        &nbsp

                                        <button type="submit" class="btn btn-default">
                                           <a href=""><i class="fa fa-search" aria-hidden="true"></i></a>
                                        </button>
                                        
                                        <button class="btn btn-default"><a  href="{{route('admin.orders.index')}}"><i class="fa fa-eraser" aria-hidden="true"></i></button>
                                        </a>
                                           
                                        </span>

                                    </div>
                                    
                                </div>

                            </div>

                        </form>
                        
                        <table class="table table-striped table-bordered sourced-data">
                                    
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('payment_id') }}</th>
                                    <th>{{ tr('user') }}</th>
                                    <th>{{ tr('delivery_price') }}</th>
                                    <th>{{ tr('sub_total') }}</th>
                                    <th>{{ tr('total') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($order_payments as $i => $order_payment_details)

                                    <tr>
                                        <td>{{ $i + 1 }}</td>

                                        <td>
                                            {{ $order_payment_details->payment_id }}
                                        </td>

                                        <td><a href="{{route('admin.users.view',['user_id' => $order_payment_details->user_id])}}">{{ $order_payment_details->userDetails->name ?? "-" }}</a></span></td>

                                        <td>
                                            {{ $order_payment_details->delivery_price_formatted}}
                                        </td>

                                        <td>{{$order_payment_details->sub_total_formatted}}</td>

                                        <td>{{$order_payment_details->total_formatted}}</td>

                                        <td><a class="btn btn-primary" href="{{route('admin.order.payments.view',['order_payment_id' => $order_payment_details->id])}}">{{tr('view')}}</a></td>

                                    </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection