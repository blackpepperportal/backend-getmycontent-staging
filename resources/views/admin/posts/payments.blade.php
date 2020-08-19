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


                        <form method="GET" action="{{route('admin.post.payments')}}">

                            <div class="row">

                                <div class="col-6">
                                    @if(Request::has('search_key'))
                                        <p class="text-muted">{{tr('search_results_for')}}<b>{{Request::get('search_key')}}</b></p>
                                    @endif
                                </div>

                                <div class="col-6">

                                    <div class="input-group">
                                       
                                        <input type="text" class="form-control" name="search_key"
                                        placeholder="{{tr('post__payment_search_placeholder')}}"> <span class="input-group-btn">
                                        &nbsp

                                        <button type="submit" class="btn btn-default">
                                           <a href=""><i class="fa fa-search" aria-hidden="true"></i></a>
                                        </button>
                                        
                                        <button class="btn btn-default"><a  href="{{route('admin.post.payments')}}"><i class="fa fa-eraser" aria-hidden="true"></i></button>
                                        </a>
                                           
                                        </span>

                                    </div>
                                    
                                </div>

                            </div>

                        </form>
                        <br>

                        <table class="table table-striped table-bordered sourced-data">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('user') }}</th>
                                    <th>{{ tr('post')}}</th>
                                    <th>{{ tr('payment_id') }}</th>
                                    <th>{{ tr('paid_amount') }}</th>
                                    <th>{{ tr('payment_mode') }}</th>
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

                                    <td>
                                        <a href="{{  route('admin.posts.view' , ['post_id' => $post_payment_details->post_id] )  }}">
                                        {{$post_payment_details->postDetails->unique_id ?? "-" }}
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