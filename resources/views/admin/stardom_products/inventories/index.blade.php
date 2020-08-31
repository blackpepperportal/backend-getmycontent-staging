@extends('layouts.admin') 

@section('title', tr('product_inventories')) 

@section('content-header', tr('product_inventories')) 

@section('breadcrumb')

<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ tr('home') }}</a>
</li>

<li class="breadcrumb-item"><a href="#">{{ tr('product_inventories') }}</a>
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

                        <table class="table table-striped table-bordered sourced-data">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('stardom_product') }}</th>
                                    <th>{{ tr('total_quatity') }}</th>
                                    <th>{{ tr('used_quatity') }}</th>
                                    <th>{{ tr('remaining_quatity') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($product_inventories as $i => $product_inventory_details)
                                <tr>
                                    <td>{{ $i+$product_inventories->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.product_inventories.view' , ['stardom_product_id' => $product_inventory_details->id] )  }}">
                                        {{ $product_inventory_details->stardomProductDetails->name ?? "-" }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ $product_inventory_details->total_quantity_formatted}}
                                    </td>

                                     <td>
                                        {{ $product_inventory_details->used_quantity_formatted}}
                                    </td>

                                     <td>
                                        {{ $product_inventory_details->remaining_quantity_formatted}}
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