@extends('layouts.admin')

@section('title', tr('edit_document'))

@section('content-header', tr('edit_document'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.documents.index')}}">{{tr('documents')}}</a></li>
    
    <li class="breadcrumb-item active">{{tr('edit_document')}}</a></li>

@endsection

@section('content')

    @include('admin.documents._form')

@endsection

