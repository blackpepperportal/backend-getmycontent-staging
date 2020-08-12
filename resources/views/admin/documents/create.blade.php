@extends('layouts.admin')

@section('title', tr('add_document'))

@section('content-header', tr('add_document'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{tr('home')}}</a>
    </li>
    
    <li class="breadcrumb-item"><a href="{{route('admin.users.index')}}">{{tr('documents')}}</a></li>

    <li class="breadcrumb-item active">{{tr('add_document')}}</a></li>

@endsection

@section('content')

    @include('admin.documents._form')

@endsection
