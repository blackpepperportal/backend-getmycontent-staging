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

                                 <div class="col-3">
                                    @if(Request::has('search_key'))
                                        <p class="text-muted">Search results for <b>{{Request::get('search_key')}}</b></p>
                                    @endif
                                </div>

                                <div class="col-3">

                                    <select class="form-control select2" name="status">

                                        <option  class="select-color" value="">{{tr('select_status')}}</option>

                                        <option  class="select-color" value="{{SORT_BY_ORDER_PLACED}}">{{tr('order_placed')}}</option>

                                        <option  class="select-color" value="{{SORT_BY_ORDER_SHIPPED}}">{{tr('order_shipped')}}</option>

                                        <option  class="select-color" value="{{SORT_BY_ORDER_DELIVERD}}">{{tr('order_deliverd')}}</option>

                                        <option  class="select-color" value="{{SORT_BY_ORDER_CANCELLED}}">{{tr('order_cancelled')}}</option>

                                    </select>

                                </div>

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
                                    <th>{{ tr('user') }}</th>
                                    <th>{{ tr('delivery_address') }}</th>
                                    <th>{{ tr('total_products') }}</th>
                                    <th>{{ tr('total')}}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($orders as $i => $order_details)

                                <tr>
                                    <td>{{ $i + $orders->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $order_details->user_id] )  }}">
                                        {{ $order_details->userDetails->name ?? "-" }}
                                        </a>
                                    </td>

                                    <td>{{ $order_details->deliveryAddressDetails->name ?? "-" }}</td>

                                    <td>
                                        {{ $order_details->total_products}}
                                    </td>

                                    <td>{{$order_details->total_formatted}}</td>

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