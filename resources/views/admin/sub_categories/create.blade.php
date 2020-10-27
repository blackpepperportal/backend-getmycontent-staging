@extends('layouts.admin')

@section('title', tr('add_sub_category'))

@section('content-header', tr('sub_categories'))

@section('breadcrumb')

    
    
    <li class="breadcrumb-item"><a href="{{route('admin.sub_categories.index')}}">{{tr('sub_categories')}}</a></li>

    <li class="breadcrumb-item active">{{tr('add_sub_category')}}</a></li>

@endsection

@section('content')

    @include('admin.sub_categories._form')

@endsection
