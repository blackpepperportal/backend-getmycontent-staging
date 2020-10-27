@extends('layouts.admin') 

@section('title', tr('view_static_page'))

@section('content-header',tr('static_pages'))

@section('breadcrumb')

    

    <li class="breadcrumb-item"><a href="{{route('admin.static_pages.index')}}">{{tr('static_pages')}}</a></li>

    <li class="breadcrumb-item active" aria-current="page">
        <span>{{tr('view_static_page')}}</span>
    </li>
           
@endsection  

@section('content')

<section id="basic-form-layouts">
    
    <div class="row match-height">
    
        <div class="col-lg-12">

            <div class="card">
                
                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title" id="basic-layout-form">{{tr('view_static_pages')}}</h4>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body">


                        <div class="card-group">

                            <div class="card mb-4">

                                <div class="card-body">

                                    <h4 class="card-title">{{ tr('description') }}</h4>
                            
                                    <p class="card-text"><?= $static_page_details->description ?></p>
                                    
                                </div>

                            </div>
                          
                            <div class="card mb-4">

                                <div class="card-body">

                                    <div class="custom-card">
                                    
                                        <h5 class="card-title">{{tr('title')}}</h5>
                                        
                                        <p class="card-text">{{$static_page_details->title}}</p>

                                    </div> 
                                    <hr>

                                    <div class="custom-card">
                                    
                                        <h5 class="card-title">{{tr('static_page_type')}}</h5>
                                        
                                        <p class="card-text">{{$static_page_details->type}}</p>

                                    </div>
                                    <hr>

                                    <div class="custom-card">
                                    
                                        <h5 class="card-title">{{tr('status')}}</h5>
                                        
                                        <p class="card-text">

                                            @if($static_page_details->status == APPROVED)

                                                <span class="badge badge-success badge-md text-uppercase">{{tr('approved')}}</span>

                                            @else 

                                                <span class="badge badge-danger badge-md text-uppercase">{{tr('pending')}}</span>

                                            @endif
                                        
                                        </p>

                                    </div>
                                    <hr>
                                                            
                                    <div class="custom-card">
                                    
                                        <h5 class="card-title">{{tr('updated_at')}}</h5>
                                        
                                        <p class="card-text">{{ common_date($static_page_details->updated_at,Auth::guard('admin')->user()->timezone) }}</p>

                                    </div>
                                    <hr>

                                    <div class="custom-card">
                                    
                                        <h5 class="card-title">{{tr('created_at')}}</h5>
                                        
                                        <p class="card-text">{{ common_date($static_page_details->created_at,Auth::guard('admin')->user()->timezone) }}</p>

                                    </div>
                                    <hr> 

                                    <div class="custom-card">
                                        <div class="row">
                                            
                                        
                                        @if(Setting::get('is_demo_control_enabled') == NO)
                                            <div class="col-md-4 col-lg-4">

                                                <a href="{{ route('admin.static_pages.edit', ['static_page_id'=> $static_page_details->id] ) }}" class="btn btn-primary btn-block">{{tr('edit')}}</a>
                                                
                                            </div>                              

                                            <div class="col-md-4 col-lg-4">
                                                <a onclick="return confirm(&quot;{{tr('static_page_delete_confirmation' , $static_page_details->title)}}&quot;);" href="{{ route('admin.static_pages.delete', ['static_page_id'=> $static_page_details->id] ) }}" class="btn btn-danger btn-block">
                                                    {{ tr('delete') }}
                                                </a>

                                            </div>                               

                                        @else
                                        
                                            <div class="col-md-4 col-lg-4">
                                                
                                                <button class="btn btn-primary btn-block" disabled>{{ tr('edit') }}</button>

                                            </div>
                                            
                                            <div class="col-md-4 col-lg-4">
                                                
                                                <button class="btn btn-warning btn-block" disabled>{{ tr('delete') }}</button>
                                            </div>
                                            

                                        @endif

                                        @if($static_page_details->status == APPROVED)

                                            <div class="col-md-4 col-lg-4">
                                                
                                                <a class="btn btn-warning btn-block" href="{{ route('admin.static_pages.status', ['static_page_id'=> $static_page_details->id] ) }}" onclick="return confirm(&quot;{{ $static_page_details->title }}-{{tr('static_page_decline_confirmation' , $static_page_details->title)}}&quot;);">

                                                    {{tr('decline')}}
                                                </a>
                                            </div>

                                        @else

                                            <div class="col-md-4 col-lg-4">
                                                 <a class="btn btn-success btn-block" href="{{ route('admin.static_pages.status', ['static_page_id'=> $static_page_details->id] ) }}">
                                                    {{tr('approve')}}
                                                </a>
                                            </div>
                                               
                                        @endif

                                        </div>

                                    </div>

                                </div>
                                <!-- Card content -->

                            </div>

                            <!-- Card -->

                        </div>

                        
                    </div>
                
                </div>

            </div>
        
        </div>
    
    </div>

</section>


@endsection
