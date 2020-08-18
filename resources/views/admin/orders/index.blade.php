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

                        <table class="table table-striped table-bordered sourced-data">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('user') }}</th>
                                    <th>{{ tr('delivery_address') }}</th>
                                    <th>{{ tr('total_products') }}</th>
                                    <th>{{ tr('tax_price') }}</th>
                                    <th>{{ tr('total')}}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($orders as $i => $order_details)

                                <tr>
                                    <td>{{ $i + $orders->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['order_id' => $order_details->id] )  }}">
                                        {{ $order_details->userDetails->name ?? "-" }}
                                        </a>
                                    </td>

                                    <td>{{ $order_details->delivaryAddressDetails->name ?? "-" }}</td>

                                    <td>
                                        {{ $order_details->total_products}}
                                    </td>

                                    <td>
                                        {{$order_details->tax_price_formatted}}
                                    </td>

                                    <td>{{$order_details->total_formatted}}</td>

                                    <td>
                                    
                                        <a class="btn btn-success" href="{{route('admin.orders.view',['order_id' => $order_details->id])}}">{{tr('view')}}</a>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $orders->appends(request()->input())->links() }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection