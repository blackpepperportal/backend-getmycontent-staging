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

            <p>{{tr('user_documents_verify_notes')}}</p>

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('verification_documents') }} <a class="" href="{{route('admin.users.view',['user_id'=> $user->id])}}">{{ $user->name ?? tr('n_a')}}</a></h4>

                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                    <div class="heading-elements">

                        @if($user_documents->count() > 0)

                            <a class="btn btn-success" href="{{route('admin.user_documents.verify', ['user_id' => $user->user_id,'status'=>USER_DOCUMENT_APPROVED])}}"><i class="icon-badge"></i> {{tr('verify')}}
                            </a>

                            <a class="btn btn-success" href="{{route('admin.user_documents.verify', ['user_id' => $user->user_id,'status'=>USER_DOCUMENT_DECLINED])}}">
                                {{tr('decline')}}
                            </a>

                        @endif

                        @if($user_documents->count() <= 0)

                            <button class="btn btn-info text-capitalize" disabled>{{tr('user_documents_waiting_upload')}}</button>

                        @endif
                    </div>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        <table class="table table-striped table-bordered sourced-data table-responsive">

                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('documents') }}</th>
                                    <th>{{ tr('updated_on') }}</th>
                                    <th>{{ tr('file') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                        
                                @foreach($user_documents as $i => $document)

                                <tr>

                                    <td>{{ $i+1 }}</td>

                                    <td>
                                        <a href="{{ route('admin.documents.view',['document_id' => $document->document_id ]) }}">{{$document->document->name ?? "-"}} </a>
                                    </td>

                                    <td>
                                        {{common_date($document->updated_at, Auth::guard('admin')->user()->timezone)}}
                                    </td>

                                    <td>
                                        <a href='{{ $document->document_file ? $document->document_file :"#"}}' target="_blank">
                                            <span class="btn btn-outline-warning btn-large">{{ tr('view') }}</span>
                                        </a>
                                    </td>

                                </tr>

                                @endforeach
                            </tbody>

                        </table>
                    </div>
                
                </div>
            </div>
        </div>
    </div>

</section>

@endsection