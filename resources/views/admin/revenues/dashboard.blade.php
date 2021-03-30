@extends('layouts.admin') 

@section('title', tr('revenue_management')) 

@section('content-header', tr('revenue_management'))

@section('breadcrumb')

<li class="breadcrumb-item active">{{ tr('revenue_dashboard') }}</li>

@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card revenue-dashboard-sec">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('dashboard') }}</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        <div class="row">

                            <div class="col-xl-3 col-lg-6 col-12">

                                <div class="card border-primary">

                                    <div class="card-content">

                                        <div class="card-body">

                                            <div class="media">

                                                <div class="media-body text-left w-100">
                                                    <h3 class="primary">{{formatted_amount($data->post_payments)}}</h3>
                                                    <span>
                                                    <a href="{{route('admin.post.payments')}}">{{tr('post_payments')}}
                                                    </a>
                                                    </span>
                                                </div>

                                                <div class="media-right media-middle">
                                                    <i class="icon-user-follow primary font-large-2 float-right"></i>
                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="col-xl-3 col-lg-6 col-12">

                                <div class="card border-primary">

                                    <div class="card-content">

                                        <div class="card-body">

                                            <div class="media">

                                                <div class="media-body text-left w-100">
                                                    <h3 class="danger">{{formatted_amount($data->user_tips)}}</h3>
                                                    <a href="{{route('admin.user_tips.index')}}">{{tr('tip_payments')}}
                                                    </a>
                                                </div>

                                                <div class="media-right media-middle">
                                                    <i class="icon-social-dropbox danger font-large-2 float-right"></i>
                                                </div>

                                            </div>
                                           
                                        </div>

                                    </div>

                                </div>

                            </div>


                            <div class="col-xl-3 col-lg-6 col-12">

                                <div class="card border-primary">

                                    <div class="card-content">

                                        <div class="card-body">

                                            <div class="media">

                                                <div class="media-body text-left w-100">
                                                    <h3 class="info">{{formatted_amount($data->subscription_payments)}}</h3>
                                                    <span>
                                                    <a href="{{route('admin.users_subscriptions.index')}}">
                                                    {{tr('subscription_payments')}}
                                                    </a>
                                                    </span>
                                                </div>

                                                <div class="media-right media-middle">
                                                    <i class="icon-credit-card info font-large-2 float-right"></i>
                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="col-xl-3 col-lg-6 col-12">

                                <div class="card border-primary">

                                    <div class="card-content">

                                        <div class="card-body">

                                            <div class="media">

                                                <div class="media-body text-left w-100">
                                                    <h3 class="success">{{formatted_amount($data->total_payments)}}</h3>
                                                    <span>{{tr('total_payments')}}</span>
                                                </div>

                                                <div class="media-right media-middle">
                                                    <i class="icon-layers success font-large-2 float-right"></i>
                                                </div>

                                            </div>
                                            
                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="col-xl-3 col-lg-6 col-12">

                                <div class="card border-primary">

                                    <div class="card-content">

                                        <div class="card-body">

                                            <div class="media">

                                                <div class="media-body text-left w-100">
                                                    <h3 class="warning">{{formatted_amount($data->today_payments)}}</h3>
                                                    <span>{{tr('today_payments')}}</span>
                                                </div>

                                                <div class="media-right media-middle">
                                                    <i class="icon-globe warning font-large-2 float-right"></i>
                                                </div>

                                            </div>
                                            
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-lg-12 col-md-12 col-sm-12 col-12">

                                <!-- <div class="card card-box">

                                <div class="card-body no-padding height-9">

                                    <div class="card-head">
                                      <div class="card-header card-title">{{tr('revenues')}}</div>
                                    </div>

                                    
                                  </div> -->
                                  <canvas id="bar-chart"></canvas>

                                </div>

                            </div>
                            
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
</section>


@endsection 

@section('scripts')

    <!-- <script src="{{asset('admin-assets/dashboard-assets/assets/plugins/chart-js/Chart.bundle.js')}}" ></script>

    <script src="{{asset('admin-assets/dashboard-assets/assets/plugins/chart-js/utils.js')}}" ></script> -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>

    <!-- <script type="text/javascript">

        $(document).ready(function() {

            new Chart(document.getElementById("bar-chart"), {
                type: 'bar',
                data: {
                    labels: [<?php foreach ($data->analytics->last_x_days_revenues as $key => $value)       {
                                echo '"'.$value->date.'"'.',';
                            } 
                            ?>],
                    datasets: [
                               {
                                   label: "Post Earnings",
                                   backgroundColor: "#3e95cd",
                                   data:[<?php 
                                            foreach ($data->analytics->last_x_days_revenues as $value) {
                                                echo $value->total_post_earnings.',';
                                            }

                                        ?>]
                                    
                               }, 

                               {
                                   label: "Subscription Earnings",
                                   backgroundColor: "#8e5ea2",
                                   data: [<?php 
                                            foreach ($data->analytics->last_x_days_revenues as $value) {
                                                echo $value->total_subscription_earnings.',';
                                            }

                                        ?>]
                               }
                               ]
                },
                options: {
                    title: {
                        display: true,
                        text: 'Total Post Earnings (in {{Setting::get('currency')}})'
                    }
                }
            });
        });

        </script> -->
        <script>
             if ($('#bar-chart').length) {
                new Chart($("#bar-chart"), {
                type: 'bar',
                data: {
                    labels: [<?php foreach ($data->analytics->last_x_days_revenues as $key => $value)       {
                                echo '"'.$value->date.'"'.',';
                            } 
                            ?>],
                    datasets: [{
                        label: "Post Earnings",
                        backgroundColor: ["#b1cfec", "#7ee5e5", "#66d1d1", "#f77eb9", "#4d8af0", "#b1cfec", "#7ee5e5", "#66d1d1", "#f77eb9", "#4d8af0"],
                        data: [<?php 
                                foreach ($data->analytics->last_x_days_revenues as $value) {
                                    echo $value->total_post_earnings.',';
                                }

                            ?>]
                    },
                    {
                        label: "Subscription Earnings",
                        backgroundColor: ["#b1cfec", "#7ee5e5", "#66d1d1", "#f77eb9", "#4d8af0", "#b1cfec", "#7ee5e5", "#66d1d1", "#f77eb9", "#4d8af0"],
                        data: [<?php 
                                foreach ($data->analytics->last_x_days_revenues as $value) {
                                    echo $value->total_subscription_earnings.',';
                                }

                            ?>]
                    }]
                },
                options: {
                    legend: { display: false },
                }
            });
    }
        </script>

@endsection
