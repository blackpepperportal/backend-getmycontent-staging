@extends('layouts.admin')

@section('title', tr('view_documents'))

@section('content-header', tr('view_documents'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{tr('home')}}</a>
    </li>
    <li class="breadcrumb-item"><a href="{{route('admin.documents.index')}}">{{tr('documents')}}</a>
    </li>
    <li class="breadcrumb-item active">{{tr('view_documents')}}</a>
    </li>

@endsection

@section('content')

<div class="content-body">

    <div class="card">

        <div class="card-header">

            <h4 id="basic-forms" class="card-title">{{$document_details->name}} {{tr('documentation')}}</h4>

        </div>
        
        <hr>

        <div class="card-content collapse show" aria-expanded="true">

            <div class="card-body">

                <div class="row">

                    <div class="col-md-3">
                        <div class="card-title">{{tr('document_image')}}</div>

                        <img src="{{$document_details->picture ?: asset('placeholder.png')}}" class="document-image">
                    </div>

                    <div class="col-md-3">

                        <div class="card-title">{{tr('action')}}</div>

                         @if(Setting::get('admin_delete_control') == YES )

                            <a href="javascript:;" class="btn btn-warning mb-2" title="{{tr('edit')}}"><b>{{tr('edit')}}</b></a>

                            <a onclick="return confirm(&quot;{{ tr('document_delete_confirmation', $document_details->title ) }}&quot;);" href="javascript:;" class="btn btn-danger" title="{{tr('delete')}}"><b>{{tr('delete')}}</b>
                                </a>

                        @else
                            <a href="{{ route('admin.documents.edit' , ['document_id' => $document_details->id] ) }}" class="btn btn-warning btn-min-width mr-1 mb-1" title="{{tr('edit')}}"><b>{{tr('edit')}}</b></a>  
                                                        
                            <a onclick="return confirm(&quot;{{ tr('document_delete_confirmation', $document_details->name ) }}&quot;);" href="{{ route('admin.documents.delete', ['document_id' => $document_details->id] ) }}" class="btn btn-danger btn-min-width mr-1 mb-1" title="{{tr('delete')}}"><b>{{tr('delete')}}</i></b>
                                </a>
                        @endif

                        @if($document_details->status == APPROVED)

                            <a class="btn btn-danger btn-min-width mr-1 mb-1" title="{{tr('decline')}}" href="{{ route('admin.documents.status', ['document_id' => $document_details->id]) }}" onclick="return confirm(&quot;{{$document_details->name}} - {{tr('document_decline_confirmation')}}&quot;);" >
                                <b>{{tr('decline')}}</b>
                            </a>

                        @else
                            
                            <a class="btn btn-success btn-min-width mr-1 mb-1" title="{{tr('approve')}}" href="{{ route('admin.documents.status', ['document_id' => $document_details->id]) }}">
                                <b>{{tr('approve')}}</b> 
                            </a>
                               
                        @endif
                           
                    </div>

                    <div class="col-lg-6">

                        <div class="card-title">{{tr('document_details')}}</div>

                        <p><strong>{{tr('name')}}</strong>

                            <span class="pull-right">{{$document_details->name}}
                            </span>
                            
                        </p>
                        <hr>

                        <p><strong>{{tr('is_required')}}</strong>

                            @if($document_details->is_required == YES)
                                <span class="badge bg-success pull-right">{{tr('yes')}}</span>
                            @else
                                <span class="badge bg-danger pull-right">{{tr('no')}}</span>
                            @endif

                        </p>
                        <hr>

                       <p><strong>{{tr('status')}}</strong>

                            @if($document_details->status == APPROVED)
                                <span class="badge bg-success pull-right">{{tr('approved')}}</span>
                            @else
                                <span class="badge bg-danger pull-right">{{tr('declined')}}</span>
                            @endif

                        </p>
                        <hr>

                        <p><strong>{{tr('created_at')}} </strong>
                            <span class="pull-right">{{common_date($document_details->created_at , Auth::guard('admin')->user()->timezone)}}</span>
                        </p>
                        <hr>

                        <p><strong>{{tr('updated_at')}} </strong>
                            <span class="pull-right">{{common_date($document_details->updated_at , Auth::guard('admin')->user()->timezone)}}
                            </span>
                        </p>
                        <hr>

                        <p><strong>{{tr('description')}}</strong></p>
                        <span>{{$document_details->description ?: "-"}}</span>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
  
@endsection

