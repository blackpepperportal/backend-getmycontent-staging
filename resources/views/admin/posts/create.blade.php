@extends('layouts.admin')

@section('title', tr('posts'))

@section('content-header', tr('posts'))

@section('styles')

    <link rel="stylesheet" href="{{asset('admin-assets/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css')}}">

@endsection


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

	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

	<script src="{{asset('admin-assets/bootstrap-datetimepicker/js/moment.min.js')}}"></script> 

    <script src="{{asset('admin-assets/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js')}}"></script> 

	<script type="text/javascript">

		$('#datepicker').datetimepicker({
	        minDate: moment(),
	        autoclose:true,
	        format:'dd-mm-yyyy hh:ii',
	    });
			
	function select_publish_type(){
		
         if($('input[name="publish_type"]:checked').val() == {{UNPUBLISHED}}){
			$('.schedule_time').css("display", "block");
		}else{
			$('.schedule_time').css("display", "none");
		}
		
	}

</script>

@endsection