@extends('layouts.admin')

@section('title', tr('content_creators'))

@section('content-header', tr('content_creators'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{tr('home')}}</a>
    </li>

    <li class="breadcrumb-item"><a href="{{route('admin.content_creators.index')}}">{{tr('content_creators')}}</a></li>
    
    <li class="breadcrumb-item active">{{tr('add_stardom')}}</a></li>

@endsection

@section('content')

    @include('admin.stardoms._form')

@endsection
