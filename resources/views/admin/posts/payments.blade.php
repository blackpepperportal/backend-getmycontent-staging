@extends('layouts.admin') 

@section('title', tr('payments')) 

@section('content-header', tr('payments')) 

@section('breadcrumb')

<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ tr('home') }}</a>
</li>
<li class="breadcrumb-item"><a href="">{{ tr('payments') }}</a>
</li>

<li class="breadcrumb-item active">{{ tr('post_payments') }}
</li>

@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('post_payments') }}</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">


                        <table class="table table-striped table-bordered sourced-data">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('user') }}</th>
                                    <th>{{ tr('payment_id') }}</th>
                                    <th>{{ tr('paid_amount') }}</th>
                                    <th>{{ tr('payment_mode') }}</th>
                                    <th>{{ tr('is_failed') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($post_payments as $i => $post_payment_details)
                                <tr>
                                    <td>{{ $i+$post_payments->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $post_payment_details->user_id] )  }}">
                                        {{ $post_payment_details->userDetails->name ?? "-" }}
                                        </a>
                                    </td>

                                    <td>{{ $post_payment_details->payment_id }}</td>

                                    <td>
                                        {{ $post_payment_details->paid_amount_formatted}}
                                    </td>

                                    <td>
                                        <span class="badge badge-secondary">
                                        {{ $post_payment_details->payment_mode}}
                                        </span>
                                    </td>

                                    <td>
                                        @if($post_payment_details->is_failed)
                                            <span class="badge badge-success">{{tr('yes')}}</span>
                                        @else
                                            <span class="badge badge-danger">{{tr('no')}}</span>
                                        @endif
                                    </td>

                                    <td>
                                       <a href="{{route('admin.post.payments.view',['post_payment_id' => $post_payment_details->id])}}" class="btn btn-primary">{{tr('view')}}</a>
                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $post_payments->appends(request()->input())->links() }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection