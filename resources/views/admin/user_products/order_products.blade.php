@extends('layouts.admin') 

@section('title', tr('products')) 

@section('content-header', tr('products')) 

@section('breadcrumb')

<li class="breadcrumb-item active"><a href="">{{ tr('products') }}</a></li>

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

    </div>

</section>

@endsection