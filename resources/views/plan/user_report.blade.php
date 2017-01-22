@extends('layout.main')

@section('content')
@include('others.print_buttons')

<div class="container-fluid page-content1">
              <h2><i class="glyphicon glyphicon-user"></i> {{ $userData[0]->name }} </h2>
              <div class="col-sm-12" id="reportTable"></div>
</div>
<script>

  function getPlan(weekStart) {
        var url = '{{url("plan/user-report/{$company}/{$uid}?t={$_GET["t"]}")}}';
        $.get(url,
                { from: weekStart }
                , function( data ) {
                  $( "#reportTable" ).html(data);
        });
  }
  getPlan('{{date('Y-m-d', strtotime("last Saturday"))}}');
</script>
@endsection

@section('footer_inc')

@endsection
