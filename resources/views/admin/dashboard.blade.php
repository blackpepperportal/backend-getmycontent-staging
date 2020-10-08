@extends('layouts.admin')

@section('content-header', tr('dashboard'))

@section('breadcrumb')
    

    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{tr('home')}}</a>
    </li>

    <li class="breadcrumb-item active">{{tr('dashboard')}}</a>
    </li>

@endsection

@section('content')

<div class="content-body dashboard-sec">

    <div class="row">

        <div class="col-xl-3 col-lg-6 col-12">

            <div class="card card-top">

                <div class="card-content">

                    <div class="media align-items-stretch">

                        <div class="p-1 text-center bg-info bg-darken-2">
                            <a href="{{route('admin.users.index')}}"><i class="icon-user font-large-2 white"></i></a>
                        </div>

                        <div class="p-1 bg-gradient-x-info white media-body">
                            <h5>{{tr('total_users')}}</h5>
                            <h5 class="text-bold-400 mb-2">{{$data->total_users}}</h5>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-xl-3 col-lg-6 col-12">

            <div class="card card-top bg-gradient-x-danger">

                <div class="card-content">

                    <div class="media align-items-stretch">

                        <div class="p-1 text-center">
                            <a href="{{route('admin.content_creators.index')}}"><i class="icon-user font-large-2 white"></i></a>
                        </div>

                        <div class="p-1 white media-body">
                            <h5>{{tr('content_creators')}}</h5>
                            <h5 class="text-bold-400 mb-0">{{$data->total_content_creators}}</h5>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-xl-3 col-lg-6 col-12">

            <div class="card card-top">

                <div class="card-content">

                    <div class="media align-items-stretch">

                        <div class="p-1 text-center bg-warning bg-darken-2">
                            <a href="{{route('admin.posts.index')}}"><i class="icon-basket-loaded font-large-2 white"></i></a>
                        </div>

                        <div class="p-1 bg-gradient-x-warning white media-body">
                            <h5>{{tr('posts')}}</h5>
                            <h5 class="text-bold-400 mb-2">{{$data->total_posts}}</h5>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-xl-3 col-lg-6 col-12">

            <div class="card card-top">

                <div class="card-content">

                    <div class="media align-items-stretch">

                        <div class="p-1 text-center bg-success bg-darken-2">
                            <a href="{{route('admin.revenues.dashboard')}}"><i class="icon-wallet font-large-2 white"></i></a>
                        </div>

                        <div class="p-1 bg-gradient-x-success white media-body">
                            <h5>{{tr('revenue')}}</h5>
                            <h5 class="text-bold-400 mb-2">{{formatted_amount($data->total_revenue)}}</h5>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="row match-height margin-top">
        
        <div class="col-xl-12 col-lg-12">

            <div class="card">

                <div class="card-header">

                    <h4 class="card-title">{{tr('revenue')}}</h4>

                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                    <div class="heading-elements">

                        <ul class="list-inline mb-0">
                            <li>
                                <a data-action="reload"><i class="ft-rotate-cw"></i></a>
                            </li>
                            <li>
                                <a data-action="expand"><i class="ft-maximize"></i></a>
                            </li>
                        </ul>

                    </div>

                </div>

                <div class="card-content">

                    <div class="card-body">

                        <div id="products-sales" class="height-300"></div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="row match-height">

        <div class="col-xl-6 col-lg-12">

            <div class="card">

                <div class="card-body">

                    <div class="card-header">

                        <div class="d-flex justify-content-between">

                            <h4 class="card-title">{{tr('recent_users')}}</h4>

                        </div>

                    </div>

                    @forelse($data->recent_users as $i => $user_details)
                    
                        <a href="{{ route('admin.users.view', ['user_id' => $user_details->id])}}" class="nav-link">

                            <div class="wrapper d-flex align-items-center py-2 border-bottom">
                                
                                <img class="img-sm rounded-circle" src="{{ $user_details->picture }}" alt="profile">

                                <div class="wrapper ml-3">
                                    <h6 class="ml-1 mb-1">
                                        {{$user_details->name}} 
                                    </h6>

                                    <small class="text-muted mb-0">
                                        <i class="icon icon-envelope-open mr-1"></i>
                                        {{ $user_details->email }}
                                        
                                    </small>
                                    <br>

                                </div>
                                
                                <small class="text-muted ml-auto">{{$user_details->created_at->diffForHumans()}}</small>
                            </div>
                        </a>

                    @empty
                        <p align="center">
                            <a href="{{route('admin.content_creators.index')}}" class="text-uppercase btn btn-success btn-xs">
                                {{tr('view_all')}}
                            </a>
                        </p>
                    @endforelse

                    @if($data->recent_users->count() > 10)

                        <p align="center">
                            <a href="{{route('admin.users.index')}}" class="text-uppercase btn btn-success btn-xs wrapper">{{tr('view_all')}}</a>
                        </p>

                    @endif

                </div>

            </div>

        </div>

        <div class="col-xl-6 col-lg-12">

            <div class="card">

                <div class="card-body">

                    <div class="card-header">

                        <div class="d-flex justify-content-between">

                            <h4 class="card-title">{{tr('recent_content_creators')}}</h4>
                            
                        </div>

                    </div>

                    @forelse($data->recent_content_creators as $i => $creator_details)

                        <a href="{{ route('admin.users.view', ['provider_id' => $creator_details->id])}}" class="nav-link">

                            <div class="list d-flex align-items-center border-bottom py-2">

                                <img class="img-sm rounded-circle" src="{{ $creator_details->picture ?: asset('placeholder.jpg')}}" alt="">

                                <div class="wrapper w-100 ml-3">

                                    <p class="mb-0"><b>{{$creator_details->name}} </b></p>

                                    <div class="d-flex justify-content-between align-items-center">

                                        <div class="d-flex align-items-center">
                                            <i class="icon icon-envelope-open text-muted mr-1"></i>

                                            <p class="mb-0 text-muted">{{$creator_details->email}}</p>
                                        </div>

                                        <small class="text-muted ml-auto">{{$creator_details->created_at->diffForHumans()}}</small>
                                    </div>
                              
                                </div>
                           
                            </div>
                                                    
                        </a>

                    @empty
                        <div class="text-center m-5">
                            <h2 class="text-muted">
                                <i class="fa fa-inbox"></i>
                            </h2>
                            <p>{{tr('no_result_found')}}</p> 
                        </div>
                    @endforelse

                     @if($data->recent_content_creators->count() > 10)
                        <p align="center">
                            <a href="{{route('admin.content_creators.index')}}" class="text-uppercase btn btn-success btn-xs">
                                {{tr('view_all')}}
                            </a>
                        </p>
                    @endif
                </div>

            </div>
        
        </div>

    </div>

</div>


@endsection

@section('scripts')
  
  <script src="{{asset('admin-assets/vendors/js/charts/raphael-min.js')}}" type="text/javascript"></script>

  <script src="{{asset('admin-assets/vendors/js/charts/morris.min.js')}}" type="text/javascript"></script>

  <script type="text/javascript">

    $(window).on("load", function () {

        $("#recent-buyers").perfectScrollbar({

            wheelPropagation: !0

        });

        var e = [<?php foreach ($data->analytics->last_x_days_revenues as $key => $value)       {
                        echo '"'.$value->formatted_month.'"'.',';
                    } 
                    ?>];

        Morris.Area({

            element: "products-sales",
            data: [
            {
                month: "2017-01",
                electronics: 0,
                apparel: 0,
                decor: 0
            }, {
                month: "2017-02",
                electronics: 0,
                apparel: 200,
                decor: 0
            }, {
                month: "2017-03",
                electronics: 0,
                apparel: 0,
                decor: 0
            }, {
                month: "2017-04",
                electronics: 0,
                apparel: 190,
                decor: 0
            }, {
                month: "2017-05",
                electronics: 0,
                apparel: 25,
                decor: 80
            }, {
                month: "2017-06",
                electronics: 0,
                apparel: 150,
                decor: 0
            }, {
                month: "2017-07",
                electronics: 0,
                apparel: 0,
                decor: 0
            }, {
                month: "2017-08",
                electronics: 80,
                apparel: 0,
                decor: 0
            }, {
                month: "2017-09",
                electronics: 0,
                apparel: 0,
                decor: 0
            }, {
                month: "2017-10",
                electronics: 0,
                apparel: 0,
                decor: 150
            }, {
                month: "2017-11",
                electronics: 300,
                apparel: 0,
                decor: 0
            }, {
                month: "2017-12",
                electronics: 0,
                apparel: 0,
                decor: 0
            }],
            xkey: "month",
            ykeys: ["electronics", "apparel", "decor"],
            labels: ["Electronics", "Apparel", "Decor"],
            xLabelFormat: function (r) {
                return e[r.getMonth()]
            },
            dateFormat: function (r) {
                return e[new Date(r).getMonth()]
            },
            behaveLikeLine: !0,
            ymax: 300,
            resize: !0,
            pointSize: 0,
            pointStrokeColors: ["#00B5B8", "#FA8E57", "#F25E75"],
            smooth: !0,
            gridLineColor: "#E4E7ED",
            numLines: 6,
            gridtextSize: 14,
            lineWidth: 0,
            fillOpacity: .9,
            hideHover: "auto",
            lineColors: ["#00B5B8", "#FA8E57", "#F25E75"]
        })
    });
  </script>

@endsection