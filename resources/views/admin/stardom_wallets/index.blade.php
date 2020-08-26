@extends('layouts.admin') 

@section('title', tr('stardom_wallets')) 

@section('content-header', tr('stardom_wallets')) 

@section('breadcrumb')

<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ tr('home') }}</a>
</li>

<li class="breadcrumb-item"><a href="{{route('admin.stardom_wallets.index')}}">{{ tr('stardom_wallets') }}</a></a>
</li>

<li class="breadcrumb-item">{{ tr('view_stardom_wallets') }}</a>
</li>

@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_stardom_wallets') }}</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        @include('admin.stardom_wallets._search')

                        <table class="table table-striped table-bordered sourced-data">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('unique_id') }} </th> 
                                    <th>{{ tr('stardom') }}</th>
                                    <th>{{ tr('total') }}</th>
                                    <th>{{ tr('onhold') }}</th>
                                    <th>{{ tr('used') }}</th>
                                    <th>{{ tr('remaining') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($stardom_wallets as $i => $stardom_wallet_details)
                                <tr>
                                    <td>{{ $i+$stardom_wallets->firstItem() }}</td>

                                    <td>{{ $stardom_wallet_details->unique_id}}</td>

                                    <td>
                                        <a href="{{  route('admin.stardoms.view' , ['stardom_id' => $stardom_wallet_details->stardom_id] )  }}">
                                        {{ $stardom_wallet_details->stardomDetails->name ?? "-" }}
                                        </a>
                                    </td>

                                    <td>{{ $stardom_wallet_details->total_formatted }}</td>

                                    <td>
                                        {{ $stardom_wallet_details->onhold_formatted}}
                                    </td>

                                    <td>
                                        {{ $stardom_wallet_details->used_formatted}}
                                    </td>

                                    <td>
                                        {{ $stardom_wallet_details->remaining_formatted}}
                                    </td>

                                    <td>
                                    
                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{ route('admin.stardom_wallets.view', ['stardom_id' => $stardom_wallet_details->stardom_id] ) }}">&nbsp;{{ tr('view') }}</a> 

                                            </div>

                                        </div>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $stardom_wallets->appends(request()->input())->links() }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection