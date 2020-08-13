@extends('layouts.admin')

@section('title', tr('view_stardom_products'))

@section('content-header', tr('view_stardom_products'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{tr('home')}}</a>
    </li>
    <li class="breadcrumb-item"><a href="{{route('admin.stardom_products.index')}}">{{tr('stardom_documents')}}</a>
    </li>
    <li class="breadcrumb-item active">{{tr('view_stardom_products')}}</a>
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
                              <img src="{{ $stardom_product_details->picture}}" alt="{{ $stardom_product_details->name}}" class="img-thumbnail img-fluid img-border height-100"
                              alt="Card image">
                            </a>
                        </div>
                        <div class="media-body pt-3 px-2">
                            <div class="row">
                                <div class="col">
                                    <h3 class="card-title">{{ $stardom_product_details->name }}</h3>
                                    <span class="text-muted">{{ $stardom_product_details->email }}</span>
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

                          <h4 class="card-title">{{tr('stardom_product_details')}}</h4>
                    </div>

                    <div class="card-content">

                        <div class="table-responsive">

                            <table class="table table-xl mb-0">
                                <tr>
                                    <th>{{tr('username')}}</th>
                                    <td>{{$stardom_product_details->name}}</td>
                                </tr>

                                <tr>
                                    <th>{{tr('email')}}</th>
                                    <td>{{$stardom_product_details->email}}</td>
                                </tr> 

                                <tr>
                                    <th>{{tr('payment_mode')}}</th>
                                    <td>{{$stardom_product_details->payment_mode}}</td>
                                </tr>
                            
                                <tr>
                                    <th>{{tr('login_type')}}</th>
                                    <td>{{$stardom_product_details->login_by}}</td>
                                </tr>

                                <tr>
                                    <th>{{tr('device_type')}}</th>
                                    <td>{{$stardom_product_details->device_type}}</td>
                                </tr>

                                <tr>
                                    <th>{{tr('status')}}</th>
                                    <td>
                                        @if($stardom_product_details->status == STARDOM_APPROVED) 

                                            <span class="badge badge-success">{{tr('approved')}}</span>

                                        @else
                                            <span class="badge badge-danger">{{tr('declined')}}</span>

                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th>{{tr('email_notification')}}</th>
                                    <td>
                                        @if($stardom_product_details->email_notification_status == YES) 

                                            <span class="badge badge-success">{{tr('yes')}}</span>

                                        @else
                                            <span class="badge badge-danger">{{tr('no')}}</span>

                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th>{{tr('push_notification')}}</th>
                                    <td>
                                        @if($stardom_product_details->push_notification_status == YES) 

                                            <span class="badge badge-success">{{tr('yes')}}</span>

                                        @else
                                            <span class="badge badge-danger">{{tr('no')}}</span>

                                        @endif
                                    </td>
                                </tr>
                                
                                <tr>
                                  <th>{{tr('created_at')}} </th>
                                  <td>{{common_date($stardom_product_details->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                </tr>

                                <tr>
                                  <th>{{tr('updated_at')}} </th>
                                  <td>{{common_date($stardom_product_details->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
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

                            <a class="btn btn-outline-secondary btn-block btn-min-width mr-1 mb-1 " href="{{route('admin.stardoms.edit', ['stardom_id'=>$stardom_product_details->id] )}}"> &nbsp;{{tr('edit')}}</a>

                        </div>

                        <div class="col-4">

                            <a class="btn btn-outline-danger btn-block btn-min-width mr-1 mb-1" onclick="return confirm(&quot;{{tr('stardom_product_delete_confirmation' , $stardom_product_details->name)}}&quot;);" href="{{route('admin.stardoms.delete', ['stardom_id'=> $stardom_product_details->id] )}}">&nbsp;{{tr('delete')}}</a>

                        </div>

                        @endif

                        <div class="col-4">

                            @if($stardom_product_details->status == APPROVED)
                                 <a class="btn btn-outline-warning btn-block btn-min-width mr-1 mb-1" href="{{route('admin.stardoms.status' ,['stardom_id'=> $stardom_product_details->id] )}}" onclick="return confirm(&quot;{{$stardom_product_details->name}} - {{tr('stardom_product_decline_confirmation')}}&quot;);">&nbsp;{{tr('decline')}} </a> 
                            @else

                                <a  class="btn btn-outline-success btn-block btn-min-width mr-1 mb-1" href="{{route('admin.stardoms.status' , ['stardom_id'=> $stardom_product_details->id] )}}">&nbsp;{{tr('approve')}}</a> 
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
