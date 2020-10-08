@extends('layouts.admin') 

@section('title', tr('view_user_products')) 

@section('content-header', tr('user_products')) 

@section('breadcrumb')

<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ tr('home') }}</a>
</li>

<li class="breadcrumb-item"><a href="{{route('admin.user_products.index')}}">{{tr('user_products')}}</a>
</li>

<li class="breadcrumb-item active">{{ tr('view_user_products') }}
</li>

@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_user_products') }}</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                    <div class="heading-elements">
                        <a href="{{ route('admin.user_products.create') }}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('add_user_product') }}</a>
                    </div>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        @include('admin.user_products._search')

                        <table class="table table-striped table-bordered sourced-data">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('product_name') }}</th>
                                    <th>{{ tr('content_creator') }}</th>
                                    <th>{{ tr('quantity') }}</th>
                                    <th>{{ tr('price') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($user_products as $i => $user_product_details)
                                <tr>
                                    <td>{{ $i+1 }}</td>

                                    <td>
                                        <a href="{{  route('admin.user_products.view' , ['user_product_id' => $user_product_details->id] )  }}">
                                        {{ $user_product_details->name }}
                                        </a>
                                    </td>

                                    <td> 
                                        <a href="{{  route('admin.users.view' , ['user_id' => $user_product_details->user_id] )  }}">{{ $user_product_details->userDetails->name ?? "-"}}
                                        </a>
                                    </td>

                                    <td>{{ $user_product_details->quantity }}</td>

                                    <td>{{ $user_product_details->user_product_price_formatted}}</td>

                                    <td>
                                        @if($user_product_details->status == APPROVED)

                                            <span class="btn btn-success btn-sm">{{ tr('approved') }}</span> 
                                        @else

                                            <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span> 
                                        @endif
                                    </td>

                                    <td>
                                    
                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{ route('admin.user_products.view', ['user_product_id' => $user_product_details->id] ) }}">&nbsp;{{ tr('view') }}</a> 

                                                @if(Setting::get('is_demo_control_enabled') == YES)

                                                    <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('edit') }}</a>

                                                    <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a> 

                                                @else

                                                    <a class="dropdown-item" href="{{ route('admin.user_products.edit', ['user_product_id' => $user_product_details->id] ) }}">&nbsp;{{ tr('edit') }}</a>

                                                    <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('user_product_delete_confirmation' , $user_product_details->name) }}&quot;);" href="{{ route('admin.user_products.delete', ['user_product_id' => $user_product_details->id] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                @endif

                                                @if($user_product_details->status == APPROVED)

                                                    <a class="dropdown-item" href="{{  route('admin.user_products.status' , ['user_product_id' => $user_product_details->id] )  }}" onclick="return confirm(&quot;{{ $user_product_details->name }} - {{ tr('user_product_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                </a> 

                                                @else

                                                    <a class="dropdown-item" href="{{ route('admin.user_products.status' , ['user_product_id' => $user_product_details->id] ) }}">&nbsp;{{ tr('approve') }}</a> 

                                                @endif

                                                <div class="dropdown-divider"></div>

                                                <a  class="dropdown-item" href="{{route('admin.product_inventories.index',['user_product_id' => $user_product_details->id])}}">{{tr('inventory')}}</a>

                                                <a  class="dropdown-item" href="{{route('admin.order_products',['user_product_id' => $user_product_details->id])}}">{{tr('orders')}}</a>

                                                <a href="{{route('admin.user_products.dashboard',['user_product_id' => $user_product_details->id])}}" class="dropdown-item">{{tr('dashboard')}}</a>

                                            </div>

                                        </div>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $user_products->appends(request()->input())->links() }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection