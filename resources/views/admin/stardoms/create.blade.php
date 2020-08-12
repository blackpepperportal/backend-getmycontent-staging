@extends('layouts.admin')

@section('title', tr('add_stardom'))

@section('content-header', tr('add_stardom'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{tr('home')}}</a>
    </li>
    <li class="breadcrumb-item"><a href="{{route('admin.users.index')}}">{{tr('stardoms')}}</a></li>
    <li class="breadcrumb-item active">{{tr('add_stardom')}}</a></li>

@endsection

@section('content')

    @include('admin.stardoms._form')

@endsection
