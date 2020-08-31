@extends('layouts.admin')

@section('title', tr('stardoms'))

@section('content-header', tr('stardoms'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{tr('home')}}</a>
    </li>

    <li class="breadcrumb-item"><a href="{{route('admin.stardoms.index')}}">{{tr('stardoms')}}</a></li>
    
    <li class="breadcrumb-item active">{{tr('add_stardom')}}</a></li>

@endsection

@section('content')

    @include('admin.stardoms._form')

@endsection
