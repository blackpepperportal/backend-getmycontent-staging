@extends('layouts.admin')

@section('title', tr('posts'))

@section('content-header', tr('posts'))

@section('breadcrumb')

    <li class="breadcrumb-item">
    	<a href="{{route('admin.posts.index')}}">{{tr('posts')}}</a>
    </li>

    <li class="breadcrumb-item active">{{tr('create_post')}}</a></li>

@endsection

@section('content')

    @include('admin.posts._form')

@endsection

@section('scripts')

<script type="text/javascript">
		
	function select_publish_type(){
		
         if($('input[name="publish_type"]:checked').val() == {{UNPUBLISHED}}){
			$('.schedule_time').css("display", "block");
		}else{
			$('.schedule_time').css("display", "none");
		}
		
	}

</script>

@endsection