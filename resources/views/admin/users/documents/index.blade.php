@extends('layouts.admin')

@section('title', tr('users'))

@section('content-header', tr('users'))

@section('breadcrumb')

<li class="breadcrumb-item">
    <a href="{{ route('admin.users.index') }}">{{ tr('users') }}</a>
</li>

<li class="breadcrumb-item active">{{ tr('verification_documents') }}</li>

@endsection

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('verification_documents') }}</h4>

                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        @include('admin.users.documents._search')

                        <table class="table table-striped table-bordered sourced-data">

                            <thead>
                                <tr>
                                    <th>{{tr('s_no')}}</th>
                                    <th>{{tr('name')}}</th>
                                    <th>{{tr('email')}}</th>
                                    <th>{{tr('no_of_documents')}}</th>
                                    <th>{{tr('status')}}</th>
                                    <th>{{tr('action')}}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($users as $i => $user)

                                <tr>
                                    <td>{{$i+$users->firstItem()}}</td>

                                    <td>
                                        <a href="{{route('admin.users.view' , ['user_id' => $user->id])}}">
                                            {{ $user->name  ?? "-" }}
                                        </a>
                                    </td>

                                    <td>
                                        {{$user->email}}
                                        <span>
                                            <h6>{{$user->mobile?: tr('not_available')}}</h6>
                                        </span>
                                    </td>

                                    <td>
                                        <a class="btn btn-outline-pink" href="{{route('admin.user_documents.view', ['user_id' => $user->id])}}">
                                            {{$user->userDocuments->count()}}
                                        </a>
                                    </td>

                                    <td>
                                        @if($user->status == YES)
                                        <span class="btn btn-success btn-sm">{{tr('approved')}}</span>
                                        @else
                                        <span class="text-danger">{{tr('declined')}}</span>

                                        @endif

                                    </td>

                                    <td>

                                        @if($user->documents_count > 0 )

                                        <a class="btn btn-success" href="{{route('admin.user_documents.verify', ['user_id' => $user->user_id])}}" onclick="return confirm(&quot;{{tr('user_document_verify_confirmation')}}&quot;);">
                                            {{tr('verify')}}
                                        </a>

                                        <a class="btn btn-outline-pink" href="{{route('admin.user_documents.view', ['user_id' => $user->id])}}">
                                            {{ tr('view_all_documents') }}
                                        </a>
                                        @else

                                        <a class="btn btn-success" href="#" onClick="alert(&quot;{{tr('user_documents_empty')}}&quot;)">
                                            {{tr('verify')}}
                                        </a>
                                        @endif

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right" id="paglink">{{ $users->appends(request()->input())->links() }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection