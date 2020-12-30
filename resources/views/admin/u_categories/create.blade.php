@extends('layouts.admin')

@section('title', tr('add_ucategories'))

@section('content-header', tr('u_category'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.u_categories.index')}}">{{tr('u_categories')}}</a></li>

    <li class="breadcrumb-item active">{{tr('add_ucategories')}}</a></li>

@endsection

@section('content')

    @include('admin.u_categories._form')

@endsection
