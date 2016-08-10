<!DOCTYPE html>
<html class="full" lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="{{asset('resources/assets/images/favicon.png')}}"/>
    <title>Tacit @if(!empty($data['page_title'])) | {{$data['page_title']}}@endif</title>
    <!-- Bootstrap Core CSS -->
    <link media="all" type="text/css" rel="stylesheet" href="{{asset('resources/assets/css')}}/bootstrap.min.css">
    <link media="all" type="text/css" rel="stylesheet" href="{{asset('resources/assets/css')}}/bootstrap-datetimepicker.min.css">
    <link media="all" type="text/css" rel="stylesheet" href="{{asset('resources/assets/css')}}/mediaqueries.css">
    <link media="all" type="text/css" rel="stylesheet" href="{{asset('resources/assets/css')}}/mediaqueries2.css">
    <link media="all" type="text/css" rel="stylesheet" href="{{asset('resources/assets/Menu')}}/tinydropdown.css">
    <link media="all" type="text/css" rel="stylesheet" href="{{asset('resources/assets/css')}}/custom.css">

    <script src="{{asset('resources/assets/js/jquery-1.11.0.js')}}"></script>
    <script src="{{asset('resources/assets/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('resources/assets/js/moment.min.js')}}"></script>
    <script src="{{asset('resources/assets/js/bootstrap-datetimepicker.js')}}"></script>
    <script src="{{asset('resources/assets/js/custom.js')}}"></script>

    <link rel="stylesheet" href="{{asset('resources/assets/css')}}/print.css" type="text/css" media="print" />


    <!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<!--[endif]-->
<script src="{{asset('resources/assets/Menu')}}/tinydropdown.js"></script>


@yield('head_inc')


</head>
<body ng-app="myApp" ng-controller="gridCtrl">

<!-- Header -->
<header class="page-head" role="header">
<div class="row">

    <div class="col-md-8 col-xs-12">
        @if ($company == 1)
          @include('layout.menu_tabuk')
        @elseif ($company == 2)
          @include('layout.menu_chiesi')
        @endif
    </div>

    <div class="col-md-4 col-xs-12 topuserpart">

        <div class="dropdown top-user-menu">
            <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <i class="glyphicon glyphicon-user blue"></i> @if (!Auth::guest()) {{ Auth::user()->name }} @endif
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                <li><a href="{{url('logout')}}"><i class="glyphicon glyphicon-remove-circle blue"></i> Log out</a></li>
            </ul>
        </div>
        <img src="{{url('resources/assets/images/logo-inner.png')}}" border="0" alt=""/>
    </div>
</div>
<!-- /.container -->
<div class="container-fluid subMenu">
    @if(isset($_GET["datefrom"]) && isset($_GET["dateto"]))
        <div class="col-sm-2">
            <b>From:</b> {{ $_GET["datefrom"] }}<br>
            <b>To:</b> {{ $_GET["dateto"] }}
        </div>
        <div class="col-sm-9">
            @yield('subMenu')
        </div>
    @endif
</div>
</header>

<div class="container-fluid @if(isset($data['page']) && $data['page']=='home') page-content @else page-content1 @endif">
<div class="row">
    @if(Session::has('message'))
        <div class="alert alert-{{Session::get('alert')}} progress-bar-striped">
            {{ Session::get('message') }}
        </div>
    @endif

    @yield('content')
</div>
</div>



@include('layout.scripts')

@yield('footer_inc')
<div class="loader-modal"></div>
</body>
</html>
