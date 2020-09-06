@extends('layouts.admin') 

@section('title', tr('orders')) 

@section('content-header', tr('orders')) 

@section('breadcrumb')

<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ tr('home') }}</a>
</li>
<li class="breadcrumb-item"><a href="{{route('admin.orders.index')}}">{{ tr('orders') }}</a></li>

<li class="breadcrumb-item active">{{ tr('view_orders') }}</a>
</li>

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

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-6">

                                <div class="card-title text-primary">{{tr('address_details')}}

                                - <a href="{{ route('admin.users.view', ['user_id' => $order_details->user_id])}}">
                                {{ $order_details->userDetails->name ?? "-"}}
                                </a>

                                </div>

                                <table class="table table-bordered table-striped tab-content">
                       
                                    <tbody>
                                       
                                        <tr>
                                            <td>{{ tr('delivery_address_name')}} </td>
                                            <td>{{ $order_details->deliveryAddressDetails->name ?? "-"}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{tr('delivery_address')}}</td>
                                            <td>{{$order_details->deliveryAddressDetails->address ?? "-"}}</td>
                                        </tr>
                                        
                                        <tr>
                                            <td>{{tr('pincode')}}</td>
                                            <td>{{$order_details->deliveryAddressDetails->pincode ?? "-"}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{tr('state')}}</td>
                                            <td>{{$order_details->deliveryAddressDetails->state ?? "-"}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{tr('landmark')}}</td>
                                            <td>{{$order_details->deliveryAddressDetails->landmark ?? "-"}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{tr('contact_number')}}</td>
                                            <td>{{$order_details->deliveryAddressDetails->contact_number ?? "-"}}</td>
                                        </tr>

                                    </tbody>

                                </table>
                                
                            </div>

                            <div class="col-md-6">

                                <div class="card-title text-primary">{{tr('order_details')}}</div>

                                <table class="table table-bordered table-striped tab-content">
                       
                                    <tbody>

                                        <tr>
                                            <td>{{ tr('unique_id')}} </td>
                                            <td class="text-uppercase">{{ $order_details->unique_id}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('delivery_address')}} </td>
                                            <td>{{ $order_details->deliveryAddressDetails->name ?? "-"}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('total_products')}} </td>
                                            <td>{{ $order_details->total_products}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('sub_total') }}</td>
                                            <td>{{ $order_details->sub_total_formatted}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{tr('status')}}</td>
                                            <td>
                                                @switch($order_details->status)

                                                    @case(SORT_BY_ORDER_CANCELLED)
                                                        <span class="badge bg-danger">{{tr('cancelled')}}</span>

                                                    @case(SORT_BY_ORDER_SHIPPED)
                                                        <span class="badge bg-secondary">{{tr('shipped')}}</span>

                                                    @case(SORT_BY_ORDER_DELIVERD) 
                                                        <span class="badge bg-success">
                                                            {{tr('deliverd')}}
                                                        </span>
                                                    @default
                                                        <span class="badge bg-primary">{{tr('placed')}}</span>

                                                @endswitch
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('tax_price') }}</td>
                                            <td>{{ $order_details->tax_price_formatted}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('total') }}</td>
                                            <td>{{$order_details->total_formatted}}</td>
                                        </tr>
                                        
                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="card">

                    <div class="card-body"> 

                        <div class="card-title text-primary">{{tr('ordered_product_details')}}

                        </div>

                        <div class="card-body">

                            <div class="row">

                                <table class="table table-bordered">
                                
                                    <thead>
                                        <tr>
                                            <th>{{ tr('product') }}</th>
                                            <th>{{ tr('quantity')}}</th>
                                            <th>{{ tr('per_quantity_price') }}</th>
                                            <th>{{ tr('sub_total') }}</th>
                                            <th>{{ tr('tax_price')}}</th>
                                            <th>{{ tr('delivery_price') }}</th>
                                            <th>{{ tr('total') }}</th>
                                        </tr>
                                    </thead>
                                   
                                    <tbody>

                                        @foreach($order_products as $i => $order_product_details)

                                        <tr>

                                            <td>
                                                <a href="{{route('admin.user_products.view',['user_product_id' => $order_product_details->user_product_id])}}">{{ $order_product_details->userProductDetails->name ?? "-"}}</a>
                                            </td>

                                            <td>{{ $order_product_details->quantity}}</td>

                                            <td>{{ $order_product_details->per_quantity_price_formatted}}</td>

                                            <td>{{ $order_product_details->sub_total_formatted}}</td>

                                            <td>{{ $order_product_details->tax_price_formatted}}</td>

                                            <td>{{ $order_product_details->delivery_price_formatted}}</td>

                                            <td>{{$order_product_details->total_formatted}}</td>

                                        </tr>

                                        @endforeach

                                    </tbody>
                                
                                </table>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="card">

                    <div class="card-body"> 

                        <div class="card-title text-primary">                       
                            {{tr('order_payment_history')}}

                        </div>

                        <table class="table table-bordered table-striped tab-content">
                       
                            <tbody>
                               
                                <tr>
                                    <td>{{ tr('order_id')}} </td>
                                    <td>{{$order_payment_details->order_id ?? "-"}}</td>
                                </tr>

                                <tr>
                                    <td>{{tr('payment_id')}}</td>
                                    <td> {{ $order_payment_details->payment_id ?? "-"}}</td>
                                </tr>
                                
                                <tr>
                                    <td>{{tr('user')}}</td>
                                    <td><a href="{{route('admin.users.view',['user_id' => $order_payment_details->user_id ?? 0])}}">{{ $order_payment_details->userDetails->name ?? "-" }}</a></td>
                                </tr>

                                <tr>
                                    <td>{{tr('delivery_price')}}</td>
                                    <td>{{ $order_payment_details->delivery_price_formatted ?? "-"}}</td>
                                </tr>

                                <tr>
                                    <td>{{tr('sub_total')}}</td>
                                    <td>{{$order_payment_details->sub_total_formatted ?? "-"}}</td>
                                </tr>

                                <tr>
                                    <td>{{tr('total')}}</td>
                                    <td>{{$order_payment_details->total_formatted ?? "-"}}</td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection

