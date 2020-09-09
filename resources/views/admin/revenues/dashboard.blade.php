@extends('layouts.admin') 

@section('title', tr('revenue_dashboard')) 

@section('content-header', tr('revenue_dashboard'))


@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ tr('home') }}</a>
    </li>
    <li class="breadcrumb-item active">{{ tr('revenue_dashboard') }}</a>
    </li>

@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('revenue_dashboard') }}</h4>
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
                                                    <span>{{tr('post_payments')}}</span>
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
                                                    <h3 class="danger">{{formatted_amount($data->order_payments)}}</h3>
                                                    <span>{{tr('order_payments')}}</span>
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

                                <div class="card card-box">

                                    <div class="card-head">
                                      <div class="card-header card-title">{{tr('revenues')}}</div>
                                    </div>

                                    <div class="card-body no-padding height-9">
                                        <div class="row">
                                            <canvas id="bar-chart"></canvas>
                                        </div>
                                    </div>

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

    <script src="{{asset('admin-assets/dashboard-assets/assets/plugins/chart-js/Chart.bundle.js')}}" ></script>

    <script src="{{asset('admin-assets/dashboard-assets/assets/plugins/chart-js/utils.js')}}" ></script>

    <script type="text/javascript">

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
                                    
                               }, {
                                   label: "Order Earnings",
                                   backgroundColor: "#8e5ea2",
                                   data: [<?php 
                                            foreach ($data->analytics->last_x_days_revenues as $value) {
                                                echo $value->total_order_earnings.',';
                                            }

                                        ?>]
                               }
                               ]
                },
                options: {
                    title: {
                        display: true,
                        text: 'Total Post & Order Earnings (in {{Setting::get('currency')}})'
                    }
                }
            });
        });

        </script>

@endsection
