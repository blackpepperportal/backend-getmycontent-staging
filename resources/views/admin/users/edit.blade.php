@extends('layouts.admin')

@section('title', tr('edit_user'))

@section('content-header', tr('edit_user'))

@section('breadcrumb_left')

    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{tr('home')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('admin.users.index')}}">{{tr('users')}}</a></li>
    <li class="breadcrumb-item active">{{tr('edit_user')}}</a></li>

@endsection

@section('content')

    @include('admin.users._form')

@endsection

@section('scripts')

<script  type="text/javascript">

    $(document).ready(function() {

        var id = $("#user_id").val();

        if(id !=''){

            $("#image_preview").show();

        } else {

            $("#image_preview").hide();
        }

    });
 
    
</script>

@endsection
