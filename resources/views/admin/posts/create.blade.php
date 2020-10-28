@extends('layouts.admin')

@section('title', tr('posts'))

@section('content-header', tr('posts'))

@section('breadcrumb')

    
    
    <li class="breadcrumb-item"><a href="{{route('admin.posts.index')}}">{{tr('posts')}}</a></li>

    <li class="breadcrumb-item active">{{tr('create_post')}}</a></li>

@endsection

@section('content')

    @include('admin.posts._form')

@endsection
@section('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/11.1.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create( document.querySelector( '#ckeditor' ) )
        .catch( error => {
            console.error( error );
        } );
</script>

@endsection
