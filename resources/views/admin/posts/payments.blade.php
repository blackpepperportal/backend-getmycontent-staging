@extends('layouts.admin') 

@section('title', tr('revenue_management')) 

@section('content-header', tr('payments')) 

@section('breadcrumb')
    
<li class="breadcrumb-item"><a href="">{{ tr('payments') }}</a></li>

<li class="breadcrumb-item active">{{ tr('post_payments') }}</li>

@endsection 

@section('content')

<section id="configuration">

    <div class="row ">

        <div class="col-12 ">

            <div class="card post-payment-sec">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('post_payments') }} 
                        
                    @if($user)
                    - 
                    <a href="{{route('admin.users.view',['user_id'=>$user->id ?? ''])}}">{{$user->name ?? ''}}</a>
                    @endif
                    
                   </h4>

                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                    
                </div>

                <div class="card-content collapse show ">

                    <div class="card-body card-dashboard">

                        @include('admin.posts._payment_search')
                        
                        <table class="table table-striped table-bordered sourced-data table-responsive">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('user') }}</th>
                                    <th>{{ tr('post')}}</th>
                                    <th >{{ tr('payment_id') }}</th>
                                    <th>{{ tr('paid_amount') }}</th>
                                    <th>{{ tr('admin_amount') }}</th>
                                    <th>{{ tr('user_amount') }}</th>
                                    <th>{{ tr('payment_mode') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($post_payments as $i => $post_payment)
                                <tr>
                                    <td>{{ $i+$post_payments->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $post_payment->user_id] )  }}">
                                        {{ $post_payment->user->name ?? "-" }}
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{  route('admin.posts.view' , ['post_id' => $post_payment->post_id] )  }}">
                                        {{$post_payment->postDetails->unique_id ?? "-" }}
                                        </a>
                                    </td>

                                    <td>
                                        {{ $post_payment->payment_id }}
                                        <br>
                                        <br>
                                        <span class="text-gray">{{tr('date')}}: {{common_date($post_payment->paid_date, Auth::user()->timezone)}}</span>
                                    </td>

                                    <td>
                                        {{ $post_payment->paid_amount_formatted}}
                                    </td>


                                    <td>
                                        {{ $post_payment->admin_amount_formatted}}
                                    </td>

                                    <td>
                                        {{ $post_payment->user_amount_formatted}}
                                    </td>


                                    <td>
                                        <span class="badge badge-secondary">
                                        {{ $post_payment->payment_mode}}
                                        </span>
                                    </td>
                                        
                                    <td class="flex payments-action-left">
                                        
                                       <a href="{{route('admin.post.payments.view',['post_payment_id' => $post_payment->id])}}" class="btn btn-primary">{{tr('view')}}</a>&nbsp;
                                   
                                       <a href="{{route('admin.post_payments.send_invoice',['post_payment_id' => $post_payment->id])}}" class="btn btn-primary"><i class="fa fa-envelope"></i>&nbsp;{{tr('send_invoice')}}</a>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right resp-float-unset" id="paglink">{{ $post_payments->appends(request()->input())->links() }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection

@section('styles')
<style>
    .table th, .table td {
    padding: 0.75rem 1.5rem !important;
}
</style>
@endsection