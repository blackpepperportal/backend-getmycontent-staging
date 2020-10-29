@extends('layouts.admin') 

@section('title', tr('users')) 

@section('content-header', tr('users')) 

@section('breadcrumb')

<li class="breadcrumb-item">
    <a href="{{ route('admin.users.index') }}">{{ tr('users') }}</a>
</li>

<li class="breadcrumb-item active">{{ tr('documents') }}</li>

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

                        <table class="table table-striped table-bordered sourced-data">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('user') }}</th>
                                    <th>{{ tr('document') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('uploaded_by') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($user_documents as $i => $user_document)
                               
                                <tr>
                                    <td>{{ $i+$user_documents->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $user_document->user_id] )  }}">
                                        {{ $user_document->userDetails->name  ?? "-" }}
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{  route('admin.documents.view' , ['document_id' => $user_document->document_id] )  }}">
                                            {{$user_document->document->name ?? "-" }}
                                        </a>
                                    </td>

                                    <td>
                                        @if($user_document->userDetails->is_document_verified != USER_DOCUMENT_VERIFIED)

                                            <span class="btn btn-success btn-sm">{{ tr('verified') }}</span> 

                                        @else

                                            <span class="btn btn-warning btn-sm">{{ tr('pending') }}</span> 
                                        @endif
                                        
                                    </td>

                                    <td>
                                        <span class="badge badge-secondary">{{ $user_document->uploaded_by ?: "-" }}</span>
                                    </td>

                                    <td>
                                    
                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{$user_document->document_file}}" target="_black"> {{ tr('document_file') }}</a> 

                                                <a class="dropdown-item" href="{{$user_document->document_file_front}}" target="_black"> {{ tr('document_file_front') }}</a> 

                                                <a class="dropdown-item" href="{{$user_document->document_file_back}}" target="_blank"> {{ tr('document_file_back') }}</a> 

                                                <div class="dropdown-divider"></div>
                                                @if($user_document->is_email_verified == USER_DOCUMENT_NOT_VERIFIED)

                                                    <a class="dropdown-item" href="{{ route('admin.users.documents.verify' , ['user_document_id' => $user_document->id]) }}">
                                                        {{ tr('verify') }}
                                                    </a>

                                                @else

                                                <a class="dropdown-item" href="{{ route('admin.users.documents.verify' , ['user_document_id' => $user_document->id]) }}">
                                                    {{ tr('unverify') }}
                                                </a>@endif

                                            </div>

                                        </div>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $user_documents->appends(request()->input())->links() }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection