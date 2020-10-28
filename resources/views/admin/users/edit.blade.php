@extends('layouts.admin')

@section('title', tr('users'))

@section('content-header', tr('users'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.users.index')}}">{{tr('users')}}</a></li>
    
    <li class="breadcrumb-item active">{{tr('edit_user')}}</a></li>

@endsection

@section('content')

    @include('admin.users._form')

@endsection
