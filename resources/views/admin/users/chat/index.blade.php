@extends('layouts.admin')

@section('content-header', tr('payments'))

@section('breadcrumb')

<li class="breadcrumb-item"><a href="">{{ tr('payments') }}</a></li>

<li class="breadcrumb-item active" aria-current="page">
    <span>{{ tr('chat_asset_payment') }}</span>
</li>

@endsection

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('chat_asset_payment') }}</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        @include('admin.users.chat._search')

                        <table class="table table-striped table-bordered sourced-data table-responsive">

                            <thead>
                                <tr>
                                    <th>{{tr('s_no')}}</th>
                                    <th>{{tr('payment_id')}}</th>
                                    <th>{{tr('from_username')}}</th>
                                    <th>{{tr('to_username')}}</th>
                                    <th>{{tr('amount')}}</th>
                                    <th>{{tr('action')}}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($chat_asset_payments as $i => $chat_asset_payment)

                                <tr>
                                    <td>{{$i+$chat_asset_payments->firstItem()}}</td>

                                    <td>
                                        <a href="{{ route('admin.chat_asset_payments.view', ['chat_asset_payment_id' => $chat_asset_payment->id] ) }}"> {{ $chat_asset_payment->payment_id ?:tr('not_available')}}
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{route('admin.users.view' , ['user_id' => $chat_asset_payment->from_user_id])}}"> {{ $chat_asset_payment->fromUser->name ?:tr('not_available')}}
                                        </a>
                                    </td>

                                    <td><a href="{{route('admin.users.view' , ['user_id' => $chat_asset_payment->to_user_id])}}"> {{ $chat_asset_payment->toUser->name ?:tr('not_available') }}</a></td>

                                    <td>{{ $chat_asset_payment->amount_formatted }}</td>

                                    <td>

                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{ route('admin.chat_asset_payments.view', ['chat_asset_payment_id' => $chat_asset_payment->id] ) }}">&nbsp;{{ tr('view') }}</a>
                                            </div>

                                        </div>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>
                        <div class="pull-right" id="paglink">{{ $chat_asset_payments->appends(request()->input())->links() }}</div>


                    </div>

                </div>

            </div>

        </div>

    </div>

</section>
@endsection