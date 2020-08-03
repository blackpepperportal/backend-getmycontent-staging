@if(Session::has('flash_error'))

    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
        <strong>Oh snap! </strong> {{Session::get('flash_error')}}
    </div>
@endif

@if(Session::has('flash_success'))

    <div class="alert alert-success">

    	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>

        <strong>Well done! </strong>{{Session::get('flash_success')}}
    </div>
    
@endif

@if(Session::has('flash_warning'))

    <div class="alert alert-warning alert-dismissible" role="alert">

    	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>

        <strong>Warning! </strong>{{Session::get('flash_warning')}}
    </div>
    
@endif
