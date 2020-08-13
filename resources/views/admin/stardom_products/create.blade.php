@extends('layouts.admin')

@section('title', tr('add_stardom_product'))

@section('content-header', tr('add_stardom_product'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{tr('home')}}</a>
    </li>
    <li class="breadcrumb-item"><a href="{{route('admin.stardom_products.index')}}">{{tr('stardom_products')}}</a></li>
    <li class="breadcrumb-item active">{{tr('add_stardom_product')}}</a></li>

@endsection

@section('content')

    @include('admin.stardom_products._form')

@endsection
