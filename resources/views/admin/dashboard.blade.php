@extends('layouts.admin')

@section('content-header', tr('dashboard'))

@section('breadcrumb')

<li class="breadcrumb-item active">{{tr('dashboard')}}</li>

@endsection

@section('content')

<div class="content-body dashboard-sec">

    <div class="row">

        <div class="col-xl-2 col-lg-6 col-12">

            <div class="card card-top ">

                <div class="card-content card-css">

                    <div class="media align-items-stretch">

                        <div class="p-1 text-center bg-pink bg-darken-4">
                            <a href="{{route('admin.users.index')}}"><i class="icon-user font-large-2 white"></i></a>
                        </div>

                        <div class="p-1 media-body">
                            <h5>{{tr('total_users')}}</h5>
                            <h5 class="text-bold-400 mb-2">
                                <a href="{{route('admin.users.index')}}">{{$data->total_users}}</a>
                            </h5>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-xl-3 col-lg-6 col-12">

            <div class="card card-top card-css">

                <div class="card-content ">

                    <div class="media align-items-stretch">

                        <div class="p-1 text-center bg-pink bg-darken-4">
                            <a href="{{route('admin.block_users.index')}}"><i class="icon-user font-large-2 white"></i></a>
                        </div>

                        <div class="p-1 media-body">
                            <h5>{{tr('blocked_users')}}</h5>
                            <h5 class="text-bold-400 mb-2">
                                <a href="{{route('admin.block_users.index')}}">{{$data->blocked_users}}</a>
                            </h5>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-xl-3 col-lg-6 col-12">

            <div class="card card-top ">

                <div class="card-content ">

                    <div class="media align-items-stretch">

                        <div class="p-1 text-center bg-teal bg-darken-4">
                            <a href="{{route('admin.users.index', ['account_type' => USER_PREMIUM_ACCOUNT])}}"><i class="icon-badge font-large-2 white"></i></a>
                        </div>

                        <div class="p-1 media-body">
                            <h5>{{tr('premium_users')}}</h5>
                            <h5 class="text-bold-400 mb-2">
                                <a href="{{route('admin.users.index', ['account_type' => USER_PREMIUM_ACCOUNT])}}">
                                    {{$data->total_premium_users}}
                                </a>
                            </h5>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-xl-2 col-lg-6 col-12">

            <div class="card card-top">

                <div class="card-content">

                    <div class="media align-items-stretch">

                        <div class="p-1 text-center bg-red bg-darken-4">
                            <a href="{{route('admin.posts.index')}}"><i class="icon-basket-loaded font-large-2 white"></i></a>
                        </div>

                        <div class="p-1 media-body">
                            <h5>{{tr('posts')}}</h5>
                            <h5 class="text-bold-400 mb-2">
                                <a href="{{route('admin.posts.index')}}">
                                    {{$data->total_posts}}
                                </a>
                            </h5>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-xl-2 col-lg-6 col-12">

            <div class="card card-top">

                <div class="card-content">

                    <div class="media align-items-stretch">

                        <div class="p-1 text-center bg-warning bg-darken-4">
                            <a href="{{route('admin.revenues.dashboard')}}"><i class="icon-wallet font-large-2 white"></i></a>
                        </div>

                        <div class="p-1 media-body">
                            <h5>{{tr('revenue')}}</h5>
                            <h5 class="text-bold-500 mb-2">
                                <a href="{{route('admin.users_subscriptions.index')}}">
                                    {{formatted_amount($data->total_revenue)}}
                                </a>
                            </h5>
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

                            <div class="col text-right">
                                @if($data->recent_users->count() > 5)
                                <a href="{{route('admin.users.index')}}" class="btn btn-sm btn-primary">{{tr('view_all')}}</a>
                                @endif
                            </div>

                        </div>



                    </div>

                    @forelse($data->recent_users as $i => $user)

                    <a href="{{ route('admin.users.view', ['user_id' => $user->id])}}" class="nav-link">

                        <div class="wrapper d-flex align-items-center py-2 border-bottom">

                            <img class="img-sm rounded-circle" src="{{ $user->picture }}" alt="profile">

                            <div class="wrapper ml-3">
                                <h6 class="ml-1 mb-1">
                                    {{$user->name}}
                                </h6>

                                <small class="text-muted mb-0">
                                    <i class="icon icon-envelope-open mr-1"></i>
                                    {{ $user->email }}

                                </small>
                                <br>

                            </div>

                            <small class="text-muted ml-auto">{{$user->created_at->diffForHumans()}}</small>
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

                            <h4 class="card-title">{{tr('recent_premium_users')}}</h4>

                            @if($data->recent_premium_users->count() > 5)
                            <a href="{{route('admin.users.index',['account_type'=>YES])}}" class="btn btn-sm btn-primary">{{tr('view_all')}}</a>
                            @endif

                        </div>

                    </div>

                    @forelse($data->recent_premium_users as $i => $recent_premium_user)

                    <a href="{{ route('admin.users.view', ['provider_id' => $recent_premium_user->id])}}" class="nav-link">

                        <div class="list d-flex align-items-center border-bottom py-2">

                            <img class="img-sm rounded-circle" src="{{ $recent_premium_user->picture ?: asset('placeholder.jpeg')}}" alt="">

                            <div class="wrapper w-100 ml-3">

                                <p class="mb-0"><b>{{$recent_premium_user->name}} </b></p>

                                <div class="d-flex justify-content-between align-items-center">

                                    <div class="d-flex align-items-center">
                                        <i class="icon icon-envelope-open text-muted mr-1"></i>

                                        <p class="mb-0 text-muted">{{$recent_premium_user->email}}</p>
                                    </div>

                                    <small class="text-muted ml-auto">{{$recent_premium_user->created_at->diffForHumans()}}</small>
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

                    @if($data->recent_premium_users->count() > 10)
                    <p align="center">
                        <a href="{{route('admin.users.index', ['account_type' => USER_PREMIUM_ACCOUNT])}}" class="text-uppercase btn btn-success btn-xs">
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
    $(window).on("load", function() {

        $("#recent-buyers").perfectScrollbar({

            wheelPropagation: !0

        });

        var e = [<?php foreach ($data->analytics->last_x_days_revenues as $key => $value) {
                        echo '"' . $value->formatted_month . '"' . ',';
                    }
                    ?>
                    ];

        Morris.Area({

            element: "products-sales",
            data: <?php print_r(json_encode($data->posts_data)); ?>,
            xkey: "month",
            ykeys: ["no_of_posts","blocked_users","report_posts"],
            labels: ["No of Posts","Blocked Users","Reported Posts"],
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