@extends('layouts.admin')

@section('title', tr('view_documents'))

@section('content-header', tr('view_documents'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{tr('home')}}</a>
    </li>
    <li class="breadcrumb-item"><a href="{{route('admin.users.index')}}">{{tr('documents')}}</a>
    </li>
    <li class="breadcrumb-item active">{{tr('view_documents')}}</a>
    </li>

@endsection

@section('content')

<div class="content-body">

    <div class="card">

        <div class="card-header">

            <h4 id="basic-forms" class="card-title">{{$document_details->name}} {{tr('documentation')}}</h4>

            <div class="heading-elements">

                <ul class="list-inline mb-0">

                    <li> 
                        @if(Setting::get('admin_delete_control') == YES )

                            <a href="{{ route('admin.documents.edit', ['document_id' => $document_details->id] ) }}" class="btn btn-warning" title="{{tr('edit')}}"><b><i class="fa fa-edit"></i></b></a>

                            <a onclick="return confirm(&quot;{{ tr('document_delete_confirmation', $document_details->title ) }}&quot;);" href="javascript:;" class="btn btn-danger" title="{{tr('delete')}}"><b><i class="fa fa-trash"></i></b>
                                </a>

                        @else
                            <a href="{{ route('admin.documents.edit' , ['document_id' => $document_details->id] ) }}" class="btn btn-warning" title="{{tr('edit')}}"><b><i class="fa fa-edit"></i></b></a>  
                                                        
                            <a onclick="return confirm(&quot;{{ tr('document_delete_confirmation', $document_details->name ) }}&quot;);" href="{{ route('admin.documents.delete', ['document_id' => $document_details->id] ) }}" class="btn btn-danger" title="{{tr('delete')}}"><b><i class="fa fa-trash"></i></b>
                                </a>
                        @endif
                        
                    </li>

                    <li>
                        @if($document_details->status == APPROVED)

                            <a class="btn btn-danger" title="{{tr('decline')}}" href="{{ route('admin.documents.status', ['document_id' => $document_details->id]) }}" onclick="return confirm(&quot;{{$document_details->name}} - {{tr('document_decline_confirmation')}}&quot;);" >
                                <b><i class="fa fa-ban"></i></b>
                            </a>

                        @else
                            
                            <a class="btn btn-success" title="{{tr('approve')}}" href="{{ route('admin.documents.status', ['document_id' => $document_details->id]) }}">
                                <b><i class="fa fa-check-circle"></i></b> 
                            </a>
                               
                        @endif
                    </li>

                </ul>

            </div>

        </div>
        
        <hr>

        <div class="card-content collapse show" aria-expanded="true">

            <div class="card-body">

                <div class="row">

                    <div class="col-lg-6"> 
                        <img src="{{$document_details->picture}}" class="document-image">
                    </div>

                    <div class="col-lg-6">

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

                    </div>

                </div>

                <p><strong>{{tr('description')}}</strong></p>

                <p class="card-text">{{$document_details->description}}</p>

            </div>

        </div>

    </div>

</div>
  
@endsection

