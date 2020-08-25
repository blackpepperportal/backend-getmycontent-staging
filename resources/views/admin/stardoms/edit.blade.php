@extends('layouts.admin')

@section('title', tr('stardoms'))

@section('content-header', tr('stardoms'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{tr('home')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('admin.stardoms.index')}}">{{tr('stardoms')}}</a></li>
    <li class="breadcrumb-item active">{{tr('edit_stardom')}}</a></li>

@endsection

@section('content')

    @include('admin.stardoms._form')

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
