@extends('layouts.admin')

@section('title', tr('edit_stardom_product'))

@section('content-header', tr('stardom_products'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{tr('home')}}</a></li>

    <li class="breadcrumb-item"><a href="{{route('admin.stardom_products.index')}}">{{tr('stardom_products')}}</a></li>

    <li class="breadcrumb-item active">{{tr('edit_stardom_product')}}</a></li>

@endsection

@section('content')

    @include('admin.stardom_products._form')

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
