@extends('layouts.admin')

@section('title', tr('view_sub_sub_categories'))

@section('content-header', tr('sub_categories'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{tr('home')}}</a>
    </li>
    <li class="breadcrumb-item"><a href="{{route('admin.sub_categories.index')}}">{{tr('sub_categories')}}</a>
    </li>
    <li class="breadcrumb-item active">{{tr('view_sub_categories')}}</a>
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
                              <img src="{{ $sub_category_details->picture}}" alt="{{ $sub_category_details->name}}" class="img-thumbnail img-fluid img-border height-100"
                              alt="Card image">
                            </a>
                        </div>
                        <div class="media-body pt-3 px-2">
                            <div class="row">
                                <div class="col">
                                    <h3 class="card-title">{{ $sub_category_details->name }}</h3>
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

                          <h4 class="card-title">{{tr('sub_category_details')}}</h4>
                    </div>

                    <div class="card-content">

                        <div class="table-responsive">

                            <table class="table table-xl mb-0">
                                <tr>
                                    <th>{{tr('name')}}</th>
                                    <td>{{$sub_category_details->name}}</td>
                                </tr>

                                <tr>
                                    <th>{{tr('category_name')}}</th>
                                    <td><a href="{{route('admin.categories.view',['category_id' => $sub_category_details->category_id])}}">{{$sub_category_details->categoryDetails->name}}</a></td>
                                </tr>

                                <tr>
                                    <th>{{tr('status')}}</th>
                                    <td>
                                        @if($sub_category_details->status == APPROVED) 

                                            <span class="badge badge-success">{{tr('approved')}}</span>

                                        @else
                                            <span class="badge badge-danger">{{tr('declined')}}</span>

                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                  <th>{{tr('created_at')}} </th>
                                  <td>{{common_date($sub_category_details->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                </tr>

                                <tr>
                                  <th>{{tr('updated_at')}} </th>
                                  <td>{{common_date($sub_category_details->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
                                </tr> 

                                <tr>
                                    <th>{{tr('description')}}</th>
                                    <td>{{$sub_category_details->description}}</td>
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

                            <a class="btn btn-outline-secondary btn-block btn-min-width mr-1 mb-1 " href="{{route('admin.sub_categories.edit', ['sub_category_id'=>$sub_category_details->id] )}}"> &nbsp;{{tr('edit')}}</a>

                        </div>

                        <div class="col-4">

                            <a class="btn btn-outline-danger btn-block btn-min-width mr-1 mb-1" onclick="return confirm(&quot;{{tr('sub_category_delete_confirmation' , $sub_category_details->name)}}&quot;);" href="{{route('admin.sub_categories.delete', ['sub_category_id'=> $sub_category_details->id] )}}">&nbsp;{{tr('delete')}}</a>

                        </div>

                        @endif

                        <div class="col-4">

                            @if($sub_category_details->status == APPROVED)
                                 <a class="btn btn-outline-warning btn-block btn-min-width mr-1 mb-1" href="{{route('admin.sub_categories.status' ,['sub_category_id'=> $sub_category_details->id] )}}" onclick="return confirm(&quot;{{$sub_category_details->name}} - {{tr('sub_category_decline_confirmation')}}&quot;);">&nbsp;{{tr('decline')}} </a> 
                            @else

                                <a  class="btn btn-outline-success btn-block btn-min-width mr-1 mb-1" href="{{route('admin.sub_categories.status' , ['sub_category_id'=> $sub_category_details->id] )}}">&nbsp;{{tr('approve')}}</a> 
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
