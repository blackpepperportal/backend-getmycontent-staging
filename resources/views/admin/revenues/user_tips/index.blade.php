@extends('layouts.admin')

@section('content-header', tr('payments'))

@section('breadcrumb')

<li class="breadcrumb-item"><a href="">{{ tr('payments') }}</a></li>

<li class="breadcrumb-item active" aria-current="page">
    <span>{{ tr('tip_payments') }}</span>
</li>

@endsection

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('tip_payments') }} 

                    @if(Request::get('user_id'))
                    - 
                    <a href="{{route('admin.users.view',['user_id'=>$user->id ?? ''])}}">{{$user->name ?? ''}}</a>
                    @endif
                    
                    </h4>

                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        @include('admin.revenues.user_tips._search')

                        <table class="table table-striped table-bordered sourced-data">

                            <thead>
                                <tr>
                                    <th>{{tr('s_no')}}</th>
                                    <th>{{tr('from_username')}}</th>
                                    <th>{{tr('to_username')}}</th>
                                    <th>{{tr('post')}}</th>
                                    <th>{{tr('amount')}}</th>
                                    <th>{{tr('status')}}</th>
                                    <th>{{tr('action')}}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($user_tips as $i => $tips)

                                <tr>
                                    <td>{{$i+$user_tips->firstItem()}}</td>

                                    <td>
                                        <a href="{{route('admin.users.view' , ['user_id' => $tips->user_id])}}"> {{ $tips->from_username ?:tr('not_available')}}
                                        </a>
                                    </td>

                                    <td><a href="{{route('admin.users.view' , ['user_id' => $tips->to_user_id])}}"> {{ $tips->to_username ?:tr('not_available') }}</a></td>

                                    <td>
                                        <a href="{{route('admin.posts.view',['post_id'=>$tips->post->id ?? ''])}}">
                                        {{ $tips->post->unique_id ?? tr('not_available') }}
                                        </a>
                                    </td>

                                    <td>{{ $tips->amount_formatted }}</td>

                                    <td>

                                        @if($tips->status == APPROVED)

                                        <span class="badge bg-success">{{ tr('approved') }} </span>

                                        @else

                                        <span class="badge bg-danger">{{ tr('declined') }} </span>

                                        @endif

                                    </td>


                                    <td>

                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{ route('admin.user_tips.view', ['user_tip_id' => $tips->id] ) }}">&nbsp;{{ tr('view') }}</a>
                                            </div>

                                        </div>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>
                        <div class="pull-right" id="paglink">{{ $user_tips->appends(request()->input())->links() }}</div>


                    </div>

                </div>

            </div>

        </div>

    </div>

</section>
@endsection