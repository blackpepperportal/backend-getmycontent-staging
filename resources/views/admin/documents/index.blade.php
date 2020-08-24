@extends('layouts.admin') 

@section('title', tr('documents')) 

@section('content-header', tr('documents')) 

@section('breadcrumb')

<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ tr('home') }}</a>
</li>

<li class="breadcrumb-item active"><a href="">{{ tr('documents') }}</a>
</li>

<li class="breadcrumb-item active">{{ tr('view_documents') }}</a>
</li>
@endsection 

@section('content')

<section id="configuration">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_documents') }}</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                    <div class="heading-elements">
                        <a href="{{ route('admin.documents.create') }}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('add_document') }}</a>
                    </div>
                    
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">

                        <table class="table table-striped table-bordered sourced-data">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('name') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('is_required') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($documents as $i => $document_details)
                                <tr>
                                    <td>{{ $i+1 }}</td>

                                    <td>
                                        <a href="{{  route('admin.documents.view' , ['document_id' => $document_details->id] )  }}">
                                        {{ $document_details->name }}
                                        </a>
                                    </td>

                                    <td>
                                        @if($document_details->status == APPROVED)

                                            <span class="btn btn-success btn-sm">{{ tr('approved') }}</span>
                                        @else

                                            <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span> 
                                        @endif
                                    </td>

                                    <td>
                                        @if($document_details->is_required == YES)

                                       <span class="btn btn-success btn-sm">{{ tr('yes') }}</span>

                                        @else

                                        <span class="btn btn-danger btn-sm">{{ tr('no') }}</span> @endif
                                    </td>

                                    <td>
                                    
                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{ route('admin.documents.view', ['document_id' => $document_details->id] ) }}">&nbsp;{{ tr('view') }}</a> 

                                                @if(Setting::get('is_demo_control_enabled') == YES)

                                                    <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('edit') }}</a>

                                                    <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a> 

                                                @else

                                                    <a class="dropdown-item" href="{{ route('admin.documents.edit', ['document_id' => $document_details->id] ) }}">&nbsp;{{ tr('edit') }}</a>

                                                    <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('document_delete_confirmation' , $document_details->name) }}&quot;);" href="{{ route('admin.documents.delete', ['document_id' => $document_details->id] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                @endif

                                                @if($document_details->status == APPROVED)

                                                    <a class="dropdown-item" href="{{  route('admin.documents.status' , ['document_id' => $document_details->id] )  }}" onclick="return confirm(&quot;{{ $document_details->name }} - {{ tr('document_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                </a> 

                                                @else

                                                    <a class="dropdown-item" href="{{ route('admin.documents.status' , ['document_id' => $document_details->id] ) }}">&nbsp;{{ tr('approve') }}</a> 

                                                @endif

                                            </div>

                                        </div>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $documents->appends(request()->input())->links() }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection