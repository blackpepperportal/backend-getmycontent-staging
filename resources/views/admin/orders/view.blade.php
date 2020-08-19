@extends('layouts.admin') 

@section('title', tr('orders')) 

@section('content-header', tr('orders')) 

@section('breadcrumb')

<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ tr('home') }}</a>
</li>
<li class="breadcrumb-item"><a href="">{{ tr('orders') }}</a></li>

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

                                <div class="card-title">{{tr('address_details')}}

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

                                <div class="card-title">{{tr('order_details')}}</div>

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

                        <div class="card-title">{{tr('order_payments')}}</div>

                        <table class="table table-striped table-bordered">
                                    
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('payment_id') }}</th>
                                    <th>{{ tr('payment_mode') }}</th>
                                    <th>{{ tr('delivery_price') }}</th>
                                    <th>{{ tr('sub_total') }}</th>
                                    <th>{{ tr('tax_price')}}</th>
                                    <th>{{ tr('total')}}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($order_payments as $i => $order_payment_details)

                                    <tr>
                                        <td>{{ $i + 1 }}</td>

                                        <td>
                                            {{ $order_payment_details->payment_id }}
                                        </td>

                                        <td><span class="badge bg-secondary">{{ $order_payment_details->payment_mode }}</span></td>

                                        <td>
                                            {{ $order_payment_details->delivery_price_formatted}}
                                        </td>

                                        <td>
                                            {{$order_payment_details->tax_price_formatted}}
                                        </td>

                                        <td>{{$order_payment_details->sub_total_formatted}}</td>

                                        <td>{{$order_payment_details->total_formatted}}</td>

                                    </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                    </div>

                </div>

                 <div class="card">

                    <div class="card-body"> 

                        <div class="card-title">{{tr('ordered_product_details')}}</div>

                        <div class="card-body">

                            <div class="row">

                            @foreach($order_products as $i => $order_product_details)
                            

                                <div class="col-md-6">

                                    <table class="table table-bordered  tab-content">
                       
                                    <tbody>

                                        <tr>
                                            <td>{{ tr('unique_id')}} </td>
                                            <td class="text-uppercase">{{ $order_product_details->unique_id}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('stardom_product')}} </td>
                                            <td><a href="{{route('admin.stardom_products.view',['stardom_product_id' => $order_product_details->stardom_product_id])}}">{{ $order_product_details->stardomProductDetails->name ?? "-"}}</a></td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('quantity')}} </td>
                                            <td>{{ $order_product_details->quantity}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('per_quantity_price') }}</td>
                                            <td>{{ $order_product_details->per_quantity_price_formatted}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('sub_total') }}</td>
                                            <td>{{ $order_product_details->sub_total_formatted}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('tax_price') }}</td>
                                            <td>{{ $order_product_details->tax_price_formatted}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('delivery_price') }}</td>
                                            <td>{{ $order_product_details->delivery_price_formatted}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('total') }}</td>
                                            <td>{{$order_product_details->total_formatted}}</td>
                                        </tr>
                                        
                                    </tbody>

                                </table>
                                    
                                </div>
                            
                            @endforeach

                            </div>
                            <div class="pull-right" id="paglink">{{ $order_products->appends(request()->input())->links() }}</div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection