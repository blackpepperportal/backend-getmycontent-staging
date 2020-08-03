<!DOCTYPE html>

<html class="loading" lang="en" data-textdirection="ltr">

<head>

    <title>@yield('title')</title>   

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="description" content="{{Setting::get('site_name')}}">

    <meta name="keywords" content="{{Setting::get('site_name')}}">

    <meta name="author" content="{{Setting::get('site_name')}}">
    
    <meta name="robots" content="noindex">

    <link rel="apple-touch-icon" href="@if(Setting::get('site_logo')) {{ Setting::get('site_logo') }}  @else {{asset('admin-assets/images/ico/apple-icon-120.png') }} @endif">

    <link rel="shortcut icon" type="image/x-icon" href="{{Setting::get('site_logo')}}">
    
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i%7COpen+Sans:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/css/vendors.min.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/vendors/css/forms/icheck/icheck.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/vendors/css/forms/icheck/custom.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/vendors/css/tables/datatable/datatables.min.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/vendors/css/forms/selects/select2.min.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/css/app.min.css')}}">
   
    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/css/core/menu/menu-types/vertical-menu.min.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/css/pages/login-register.min.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/fonts/simple-line-icons/style.min.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/css/core/colors/palette-gradient.min.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('admin-assets/css/style.css')}}">

    @yield('styles')


</head>

<body class="vertical-layout vertical-menu 2-columns menu-expanded fixed-navbar" data-open="click" data-menu="vertical-menu" data-col="2-columns">
    
    @include('layouts.admin.header')

    @include('layouts.admin.sidebar')

    <div class="app-content content">
        
        <div class="content-wrapper">

            <div class="content-header row">
                
                <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                @yield('breadcrumb')
                            </ol>
                        </div>
                    </div>

                    @include('notifications.notify')
                    
                    <h3 class="content-header-title mb-0">@yield('content-header')</h3>

                </div>

            </div>

            <div class="content-body">

                @yield('content')
                
            </div>
        </div>
    
    </div>

    @include('layouts.admin.footer')

    @include('layouts.admin.scripts')

    @yield('scripts')

    <script type="text/javascript">

        @if(isset($page)) 
            $("#{{$page}}").addClass("active");
        @endif

        @if(isset($sub_page)) 
            $("#{{$sub_page}}").addClass("active");
        @endif
        
    </script>

    <script type="text/javascript">

        function alphaOnly(event) {

            var key = event.keyCode;

            return ((key >= 65 && key <= 90) || key == 8 || key == 32 || key == 9 || key == 39);
        };

         $("button[type='reset']").click(function(){

            $('#image_preview').attr('src', '').hide();
        });

        function loadFile(event, id){

              var ext=$("#picture").val();

              var fileExtension = ['jpeg','jpg','png'];
              
                if ($.inArray(ext.split('.').pop().toLowerCase(), fileExtension) == -1) {
                        
                    alert("Only formats allowed are : "+fileExtension.join(','));
                   
                    document.getElementById("picture").value = null;                    
                    
                    return false;
                    
                } else {

                    $("#"+id).show();
                  
                    var reader = new FileReader();

                    reader.onload = function(){

                    var output = document.getElementById(id);
                  
                      output.src = reader.result;
                      
                    };
                    reader.readAsDataURL(event.files[0]);
                }
        
        }

    </script>

    <?php echo Setting::get('body_scripts'); ?>

</body>

</html>