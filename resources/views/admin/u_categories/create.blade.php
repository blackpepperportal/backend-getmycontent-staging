@extends('layouts.admin')

@section('title', tr('add_ucategory'))

@section('content-header', tr('u_categories'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.u_categories.index')}}">{{tr('u_categories')}}</a></li>

    <li class="breadcrumb-item active">{{tr('add_ucategory')}}</a></li>

@endsection

@section('content')

    @include('admin.u_categories._form')

@endsection
