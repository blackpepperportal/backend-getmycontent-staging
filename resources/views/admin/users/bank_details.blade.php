@extends('layouts.admin')

@section('title', tr('view_billing_account'))

@section('content-header', tr('users'))

@section('breadcrumb')


<li class="breadcrumb-item active">
    <a href="{{route('admin.users.index')}}">{{ tr('users') }}</a>
</li>

<li class="breadcrumb-item">{{tr('view_billing_account')}}</li>

@endsection

@section('content')

<section id="configuration">
    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{tr('view_billing_account')}}</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard table-responsive">

                        <table class="table table-striped table-bordered sourced-data ">

                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('nickname') }}</th>
                                    <th>{{ tr('bank_name') }}</th>
                                    <th>{{ tr('account_holder_name') }}</th>
                                    <th>{{ tr('account_number') }}</th>
                                    <th>{{tr('ifsc_code')}}</th>
                                    <th>{{ tr('swift_code') }}</th>
                                    <th>{{ tr('is_default') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($bank_details as $i => $bank_detail)

                                <tr>

                                    <td>{{ $i+$bank_details->firstItem() }}</td>

                                    <td class="white-space-nowrap">
                                        {{$bank_detail->nickname ?: tr('not_available')}}
                                    </td>

                                    <td class="white-space-nowrap">
                                        {{ $bank_detail->bank_name ?: tr('not_available')}}
                                    </td>

                                    <td class="white-space-nowrap">
                                        {{$bank_detail->account_holder_name ?: tr('not_available')}}
                                    </td>

                                    <td>
                                        {{$bank_detail->account_number ?: tr('not_available')}}
                                    </td>

                                    <td>
                                        {{$bank_detail->ifsc_code ?: tr('not_available')}}
                                    </td>
                                    
                                    <td>
                                        {{$bank_detail->swift_code ?: tr('not_available')}}
                                    </td>

                                    <td>
                                        @if($bank_detail->is_default == YES)

                                            <span class="btn btn-success btn-sm">{{ tr('yes') }}</span>

                                        @else

                                            <span class="btn btn-danger btn-sm">{{ tr('no') }}</span>

                                        @endif
                                    </td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right" id="paglink">{{ $bank_details->appends(request()->input())->links() }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection
