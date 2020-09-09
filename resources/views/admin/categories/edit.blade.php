@extends('layouts.admin')

@section('title', tr('edit_category'))

@section('content-header', tr('categories'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{tr('home')}}</a></li>

    <li class="breadcrumb-item"><a href="{{route('admin.categories.index')}}">{{tr('categories')}}</a></li>

    <li class="breadcrumb-item active">{{tr('edit_user_product')}}</a></li>

@endsection

@section('content')

    @include('admin.categories._form')

@endsection