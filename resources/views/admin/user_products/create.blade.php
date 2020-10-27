@extends('layouts.admin')

@section('title', tr('add_user_product'))

@section('content-header', tr('user_products'))

@section('breadcrumb')

    
    <li class="breadcrumb-item"><a href="{{route('admin.user_products.index')}}">{{tr('user_products')}}</a></li>
    <li class="breadcrumb-item active">{{tr('add_user_product')}}</a></li>

@endsection

@section('content')

    @include('admin.user_products._form')

@endsection
