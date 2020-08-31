@extends('layouts.admin') 

@section('title', tr('product_inventories')) 

@section('content-header', tr('product_inventories')) 

@section('breadcrumb')

<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ tr('home') }}</a>
</li>

<li class="breadcrumb-item"><a href="{{route('admin.product_inventories.index')}}">{{ tr('product_inventories') }}</a>
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

                           <table class="table table-bordered table-striped tab-content">
                       
                                <tbody>
                                   
                                    <tr>
                                        <td>{{ tr('stardom_product')}} </td>
                                        <td><a href="{{  route('admin.product_inventories.view' , ['stardom_product_id' => $product_inventory_details->id] )  }}">{{ $product_inventory_details->stardomProductDetails->name ?? "-"}}</a></td>
                                    </tr>

                                    <tr>
                                        <td>{{tr('total_quantity')}}</td>
                                        <td>{{$product_inventory_details->total_quantity_formatted ?? "-"}}</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>{{tr('used_quantity')}}</td>
                                        <td>{{$product_inventory_details->used_quantity_formatted ?? "-"}}</td>
                                    </tr>

                                    <tr>
                                        <td>{{tr('remaining_quatity')}}</td>
                                        <td>{{$product_inventory_details->remaining_quantity_formatted ?? "-"}}</td>
                                    </tr>

                                    <tr>
                                        <td>{{tr('status')}}</td> 

                                        @if($product_inventory_details->status == APPROVED)

                                            <td class="text-success">{{ tr('approved') }}</td> 
                                        @else

                                            <td class="text-danger">{{ tr('declined') }}</td> 
                                        @endif
                                    </tr>
                                

                                    <tr>
                                        <td> {{tr('created_at')}}</td>
                                        <td>
                                            {{common_date($product_inventory_details->created_at , Auth::guard('admin')->user()->timezone)}}
                                        </td>
                                    </tr>
                                    

                                    <tr>
                                        <td>{{tr('updated_at')}}</td> 
                                        <td>
                                            {{common_date($product_inventory_details->updated_at , Auth::guard('admin')->user()->timezone)}}
                                        </td>
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