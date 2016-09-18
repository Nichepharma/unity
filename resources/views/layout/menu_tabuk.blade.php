<?php
$module = '';
if (isset($data['modules']) && !empty($data['modules']))
    $module = $data['modules'];
?>
<nav class="mainmenubg" role="navigation">
    <div class="mainmenu">
        <div class="menu_mob_btn"></div>
        <ul class="menu" id="menu">
            <li class="@if($module=='') current @endif"><a href="http://tacitapp.com/tabuk/home.php?datefrom={{$_GET['datefrom']}}&dateto={{$_GET['dateto']}}">Home</a></li>
            <li class="insights @if($module=='insights') current @endif"><a href="http://tacitapp.com/tabuk/allproducts.php?datefrom={{$_GET['datefrom']}}&dateto={{$_GET['dateto']}}">INSIGHTS</a></li>
            <li class="plan @if($module=='plan') current @endif"><a href="http://tacitapp.com/unity/plan/{{$company}}">PLAN</a></li>
            <li class="kpi @if($module=='kpi') current @endif"><a href="http://tacitapp.com/tabuk/kpi2.php?datefrom={{$_GET['datefrom']}}&dateto={{$_GET['dateto']}}">KPI</a></li>
            <li class="customers @if($module=='customers') current @endif"><a href="http://tacitapp.com/tabuk/customerslist.php?datefrom={{$_GET['datefrom']}}&dateto={{$_GET['dateto']}}">CUSTOMERS</a></li>
            <li class="applications @if($module=='applications') current @endif"><a href="http://tacitapp.com/tabuk/products.php?datefrom={{$_GET['datefrom']}}&dateto={{$_GET['dateto']}}">APPS</a></li>

        </ul>

        <script type="text/javascript">
            var dropdown = new TINY.dropdown.init("dropdown", {id: 'menu', active: 'menuhover'});
        </script>
    </div>
</nav>
{{--@if(@$data['cur_mod']=='activities'--}}

{{-- @if($data['user']->can('accounts.index')) --}}
