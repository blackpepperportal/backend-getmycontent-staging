@extends('layouts.admin')

@section('title', tr('view_user_products'))

@section('content-header', tr('user_products'))

@section('breadcrumb')

    
    <li class="breadcrumb-item"><a href="{{route('admin.user_products.index')}}">{{tr('user_products')}}</a>
    </li>
    <li class="breadcrumb-item active">{{tr('view_user_products')}}</a>
    </li>

@endsection

@section('content')

<div class="content-body">

    <div id="user-profile">

        <div class="row">

            <div class="col-12">

                <div class="card profile-with-cover">

                    <div class="media profil-cover-details w-100">
                        <div class="media-left pl-2 pt-2">
                            <a  class="profile-image">
                              <img src="{{ $user_product->picture}}" alt="{{ $user_product->name}}" class="img-thumbnail img-fluid img-border height-100"
                              alt="Card image">
                            </a>
                        </div>
                        <div class="media-body pt-3 px-2">
                            <div class="row">
                                <div class="col">
                                    <h3 class="card-title">{{ $user_product->name }}</h3>
                                </div>

                            </div>

                        </div>
                        
                    </div>

                    <nav class="navbar navbar-light navbar-profile align-self-end">
                       
                    </nav>
                </div>
            </div>
  
            <div class="col-xl-12 col-lg-12">

                <div class="card">

                    <div class="card-header border-bottom border-gray">

                          <h4 class="card-title">{{tr('user_product')}}</h4>
                    </div>

                    <div class="card-content">

                        <div class="table-responsive">

                            <table class="table table-xl mb-0">
                                <tr>
                                    <th>{{tr('name')}}</th>
                                    <td>{{$user_product->name}}</td>
                                </tr>

                                <tr>
                                    <th>{{tr('content_creator_name')}}</th>

                                    <td><a href="{{route('admin.users.view',['user_id' => $user_product->user_id])}}">{{$user_product->userDetails->name ?? "-"}}</a></td>
                                </tr>

                                <tr>
                                    <th>{{tr('quantity')}}</th>
                                    <td>{{$user_product->quantity}}</td>
                                </tr> 

                                <tr>
                                    <th>{{tr('price')}}</th>
                                    <td>{{$user_product->user_product_price_formatted}}</td>
                                </tr>

                                <tr>
                                    <th>{{tr('status')}}</th>
                                    <td>
                                        @if($user_product->status == APPROVED) 

                                            <span class="badge badge-success">{{tr('approved')}}</span>

                                        @else
                                            <span class="badge badge-danger">{{tr('declined')}}</span>

                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                  <th>{{tr('created_at')}} </th>
                                  <td>{{common_date($user_product->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                </tr>

                                <tr>
                                  <th>{{tr('updated_at')}} </th>
                                  <td>{{common_date($user_product->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
                                </tr>   
                                
                            </table>

                        </div>

                    </div>

                    <div class="card-footer">

                    <div class="row">

                        @if(Setting::get('is_demo_control_enabled') == YES)

                        <div class="col-4">

                            <a class="btn btn-outline-secondary btn-block btn-min-width mr-1 mb-1 " href="javascript:void(0)"> &nbsp;{{tr('edit')}}</a>

                        </div>

                        <div class="col-4">

                            <a class="btn btn-outline-danger btn-block btn-min-width mr-1 mb-1" href="javascript:void(0)">&nbsp;{{tr('delete')}}</a>

                        </div>


                        @else

                        <div class="col-4">

                            <a class="btn btn-outline-secondary btn-block btn-min-width mr-1 mb-1 " href="{{route('admin.user_products.edit', ['user_product_id'=>$user_product->id] )}}"> &nbsp;{{tr('edit')}}</a>

                        </div>

                        <div class="col-4">

                            <a class="btn btn-outline-danger btn-block btn-min-width mr-1 mb-1" onclick="return confirm(&quot;{{tr('user_product_delete_confirmation' , $user_product->name)}}&quot;);" href="{{route('admin.user_products.delete', ['user_product_id'=> $user_product->id] )}}">&nbsp;{{tr('delete')}}</a>

                        </div>

                        @endif

                        <div class="col-4">

                            @if($user_product->status == APPROVED)
                                 <a class="btn btn-outline-warning btn-block btn-min-width mr-1 mb-1" href="{{route('admin.user_products.status' ,['user_product_id'=> $user_product->id] )}}" onclick="return confirm(&quot;{{$user_product->name}} - {{tr('user_product_decline_confirmation')}}&quot;);">&nbsp;{{tr('decline')}} </a> 
                            @else

                                <a  class="btn btn-outline-success btn-block btn-min-width mr-1 mb-1" href="{{route('admin.user_products.status' , ['user_product_id'=> $user_product->id] )}}">&nbsp;{{tr('approve')}}</a> 
                            @endif
                        </div>

                    </div>

                </div>

                </div>

            </div>

        </div>

    </div>

</div>
  
    
@endsection

@section('scripts')

@endsection
