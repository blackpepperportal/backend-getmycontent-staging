@extends('layouts.admin')

@section('title', tr('posts'))

@section('content-header', tr('posts'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.posts.index')}}">{{tr('posts')}}</a></li>
    
    <li class="breadcrumb-item active">{{tr('edit_post')}}</a></li>

@endsection

@section('content')

    @include('admin.posts._form')

@endsection