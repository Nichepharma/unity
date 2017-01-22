<!DOCTYPE html>
<html class="full" lang="en">

<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="shortcut icon" href="favicon.ico"/>
	<title>Tacit</title>

  <link rel="stylesheet" href="{{asset('resources/assets/css/bootstrap.min.css')}}">
  <link rel="stylesheet" href="{{asset('resources/assets/css/bootstrap-datetimepicker.min.css')}}">
  <link rel="stylesheet" href="{{asset('resources/assets/css/mediaqueries.css')}}">
  <link rel="stylesheet" href="{{asset('resources/assets/css/mediaqueries2.css')}}">
  <link rel="stylesheet" href="{{asset('resources/assets/Menu/tinydropdown.css')}}">
  <link rel="stylesheet" href="{{asset('resources/assets/css/custom.css')}}">


</head>
<body class="fulllogin">
  <div class="container page-login">
  	@yield('content')
  </div><!-- ./Forget Password Part -->

    <!-- JavaScripts -->
    <script src="http://tacitapp.com/dermazone/assets/js/jquery-1.11.0.js"></script>

    <script src="http://tacitapp.com/dermazone/assets/js/bootstrap.min.js"></script>
        <script type="text/javascript">


    	$(document).ready(function(){

    		$("#forgetpart").css({'display':'none'});

    		$("#forgeturpass").click(function(){
    			$("#forgetpart").fadeIn();
    			$("#loginpart").css({'display':'none'});
    		});

    	});


    </script>
</body>
</html>
