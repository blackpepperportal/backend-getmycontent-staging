@extends('layouts.admin')

@section('title', tr('edit_ucategory'))

@section('content-header', tr('categories'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.u_categories.index')}}">{{tr('u_category')}}</a></li>

    <li class="breadcrumb-item active">{{tr('edit_ucategory')}}</a></li>

@endsection

@section('content')

    @include('admin.u_categories._form')

@endsection