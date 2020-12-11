@extends('layouts.admin')

@section('title', tr('users'))

@section('content-header', tr('users'))

@section('breadcrumb')


<li class="breadcrumb-item"><a href="{{route('admin.block_users.index')}}">{{tr('block_users')}}</a>
</li>
<li class="breadcrumb-item active">{{tr('view_block_user')}}</a>
</li>

@endsection

@section('content')

<div class="content-body">

    <div id="user-profile">

        <div class="row">

            <div class="col-xl-12 col-lg-12">

                <div class="card">

                    <div class="card-header border-bottom border-gray">

                        <h4 class="card-title">{{tr('view_users')}}</h4>

                    </div>

                    <div class="card-content">

                        <div class="col-12">

                            <div class="card profile-with-cover">

                                <div class="media profil-cover-details w-100">

                                    <div class="media-left pl-2 pt-2">

                                        <a class="profile-image">
                                            <img src="{{ $block_user->user->picture ?? asset('placeholder.jpg')}}" alt="{{ $block_user->user->name ?? ''}}" class="img-thumbnail img-fluid img-border height-100" alt="Card image">
                                        </a>

                                    </div>

                                    <div class="media-body pt-3 px-2">

                                        <div class="row">

                                            <div class="col">
                                                <h3 class="card-title">{{ $block_user->user->name ?? ''}}</h3>
                                                <span class="text-muted">{{ $block_user->user->email ?? ''}}</span>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <nav class="navbar navbar-light navbar-profile align-self-end">

                                </nav>
                            </div>
                        </div>

                        <div class="table-responsive">

                            <table class="table table-xl mb-0">
                                <tr>
                                    <th>{{tr('username')}}</th>
                                    <td>{{$block_user->user->name ?? ''}}</td>
                                </tr>

                                <tr>
                                    <th>{{tr('email')}}</th>
                                    <td>{{$block_user->user->email ?? ''}}</td>
                                </tr>

                                <tr>
                                    <th>{{tr('blocked_count')}}</th>
                                    <td>{{$block_user->blocked_count ?? ''}}</td>
                                </tr>

                               
                              
                            </table>

                        </div>

                    </div>

                   

                </div>

            </div>

        </div>

    </div>

</div>


@endsection

@section('scripts')

@endsection