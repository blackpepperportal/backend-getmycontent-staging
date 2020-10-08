@extends('layouts.admin') 

@section('title', tr('product_inventories')) 

@section('content-header', tr('product_inventories')) 

@section('breadcrumb')

<li class="breadcrumb-item">
    <a href="{{route('admin.product_inventories.index')}}">{{ tr('product_inventories') }}</a>
</li>

<li class="breadcrumb-item active">{{ tr('view_product_inventories') }}
</li>

@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">
                        {{tr('view_product_inventories')}}
                    </h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        @include('admin.user_products.inventories._search')

                        <table class="table table-striped table-bordered sourced-data">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('user_product') }}</th>
                                    <th>{{ tr('total_quantity') }}</th>
                                    <th>{{ tr('used_quantity') }}</th>
                                    <th>{{ tr('remaining_quatity') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($product_inventories as $i => $product_inventory_details)
                                <tr>
                                    <td>{{ $i+$product_inventories->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.user_products.view' , ['user_product_id' => $product_inventory_details->user_product_id] )  }}">
                                        {{ $product_inventory_details->userProductDetails->name ?? "-" }}
                                        </a>
                                    </td>
                                    
                                    <td>
                                        {{ $product_inventory_details->total_quantity}}
                                    </td>

                                     <td>
                                        {{ $product_inventory_details->used_quantity}}
                                    </td>

                                     <td>
                                        {{ $product_inventory_details->remaining_quantity}}
                                    </td>


                                    <td>
                                        <a class="btn btn-primary" href="{{ route('admin.product_inventories.view', ['product_inventory_id' => $product_inventory_details->id] ) }}">&nbsp;{{ tr('view') }}</a> 
                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $product_inventories->appends(request()->input())->links() }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection