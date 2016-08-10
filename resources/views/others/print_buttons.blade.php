@if(isset($_GET["datefrom"]) && isset($_GET["dateto"]))
<div class="pull-left print-show">
    <p><b>From:</b> {{$_GET["datefrom"]}}</p>
    <p><b>To:</b> {{$_GET["dateto"]}}</p>
</div>
@endif

<div class="text-right contenttopicon">
    @if(isset($moreLinks)) {{$moreLinks}} @endif
    <a href="{{url('home/tell-friend')}}" target="page" onclick="window.open('','page','toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=550,height=410,left=50,top=50,titlebar=yes')">
        <i class="glyphicon glyphicon-envelope"></i>
    </a>

    <a href="#" onclick="window.print();"><i class="glyphicon glyphicon-print"></i></a>

</div>
