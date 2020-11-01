@extends('layouts.admin')

@section('title', tr('view_categories'))

@section('content-header', tr('categories'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.categories.index')}}">{{tr('categories')}}</a>
    </li>
    <li class="breadcrumb-item active">{{tr('view_categories')}}</a>
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
                              <img src="{{ $category->picture}}" alt="{{ $category->name}}" class="img-thumbnail img-fluid img-border height-100"
                              alt="Card image">
                            </a>
                        </div>
                        <div class="media-body pt-3 px-2">
                            <div class="row">
                                <div class="col">
                                    <h3 class="card-title">{{ $category->name }}</h3>
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

                          <h4 class="card-title">{{tr('category')}}</h4>
                    </div>

                    <div class="card-content">

                        <div class="table-responsive">

                            <table class="table table-xl mb-0">
                                <tr>
                                    <th>{{tr('name')}}</th>
                                    <td>{{$category->name}}</td>
                                </tr>
                                <tr>
                                    <th>{{tr('status')}}</th>
                                    <td>
                                        @if($category->status == APPROVED) 

                                            <span class="badge badge-success">{{tr('approved')}}</span>

                                        @else
                                            <span class="badge badge-danger">{{tr('declined')}}</span>

                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                  <th>{{tr('created_at')}} </th>
                                  <td>{{common_date($category->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                </tr>

                                <tr>
                                  <th>{{tr('updated_at')}} </th>
                                  <td>{{common_date($category->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
                                </tr> 

                                <tr>
                                    <th>{{tr('description')}}</th>
                                    <td>{{$category->description}}</td>
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

                            <a class="btn btn-outline-secondary btn-block btn-min-width mr-1 mb-1 " href="{{route('admin.categories.edit', ['category_id'=>$category->id] )}}"> &nbsp;{{tr('edit')}}</a>

                        </div>

                        <div class="col-4">

                            <a class="btn btn-outline-danger btn-block btn-min-width mr-1 mb-1" onclick="return confirm(&quot;{{tr('category_delete_confirmation' , $category->name)}}&quot;);" href="{{route('admin.categories.delete', ['category_id'=> $category->id] )}}">&nbsp;{{tr('delete')}}</a>

                        </div>

                        @endif

                        <div class="col-4">

                            @if($category->status == APPROVED)
                                 <a class="btn btn-outline-warning btn-block btn-min-width mr-1 mb-1" href="{{route('admin.categories.status' ,['category_id'=> $category->id] )}}" onclick="return confirm(&quot;{{$category->name}} - {{tr('category_decline_confirmation')}}&quot;);">&nbsp;{{tr('decline')}} </a> 
                            @else

                                <a  class="btn btn-outline-success btn-block btn-min-width mr-1 mb-1" href="{{route('admin.categories.status' , ['category_id'=> $category->id] )}}">&nbsp;{{tr('approve')}}</a> 
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
