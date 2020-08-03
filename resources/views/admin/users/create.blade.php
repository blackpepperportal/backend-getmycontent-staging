@extends('layouts.admin')

@section('title', tr('add_user'))

@section('content-header', tr('add_user'))

@section('breadcrumb_left')

    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{tr('home')}}</a>
    </li>
    <li class="breadcrumb-item"><a href="{{route('admin.users.index')}}">{{tr('users')}}</a>    </li>
    <li class="breadcrumb-item active">{{tr('add_user')}}</a></li>

@endsection

@section('content')

    @include('admin.users._form')

@endsection
