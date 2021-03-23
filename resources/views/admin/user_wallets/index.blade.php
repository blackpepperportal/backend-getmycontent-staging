@extends('layouts.admin') 

@section('title', tr('revenue_management')) 

@section('content-header', tr('revenue_management')) 

@section('breadcrumb')

<li class="breadcrumb-item active">
    <a href="{{route('admin.user_wallets.index')}}">{{ tr('revenue_management') }}</a>
</li>

<li class="breadcrumb-item ">{{tr('wallets')}}</li>

@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{tr('wallets')}}</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        @include('admin.user_wallets._search')

                        <table class="table table-striped table-bordered sourced-data table-responsive">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('unique_id') }} </th> 
                                    <th>{{ tr('user') }}</th>
                                    <th>{{ tr('total') }}</th>
                                    <th>{{ tr('onhold') }}</th>
                                    <th>{{ tr('used') }}</th>
                                    <th>{{ tr('remaining') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($user_wallets as $i => $user_wallet_details)
                                <tr>
                                    <td>{{ $i+$user_wallets->firstItem() }}</td>

                                    <td> <a href="{{ route('admin.user_wallets.view', ['user_id' => $user_wallet_details->user_id] ) }}">{{ $user_wallet_details->unique_id}}</a></td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $user_wallet_details->user_id] )  }}">
                                        {{ $user_wallet_details->user->name ?? "-" }}
                                        </a>
                                    </td>

                                    <td>{{ $user_wallet_details->total_formatted }}</td>

                                    <td>
                                        {{ $user_wallet_details->onhold_formatted}}
                                    </td>

                                    <td>
                                        {{ $user_wallet_details->used_formatted}}
                                    </td>

                                    <td>
                                        {{ $user_wallet_details->remaining_formatted}}
                                    </td>

                                    <td>
                                    
                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{ route('admin.user_wallets.view', ['user_id' => $user_wallet_details->user_id] ) }}">&nbsp;{{ tr('view') }}</a> 

                                            </div>

                                        </div>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $user_wallets->appends(request()->input())->links() }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection