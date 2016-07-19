@extends('layout.main')

@section('content')
<div class="container-fluid page-content1">
              <h2><i class="glyphicon glyphicon-user"></i> {{ $userData[0]->name }} </h2>

              <div class="col-sm-12" id="planTable">

              </div>
</div>
<script>
  function getPlan(weekStart) {
        var url = '{{url("plan/user-plan/{$company}/{$uid}")}}';
        $.get(url,
                { from: weekStart }
                , function( data ) {
                  $( "#planTable" ).html( data );
        });
  }
  getPlan('{{date('Y-m-d', strtotime("last Saturday"))}}');
</script>
@endsection

@section('footer_inc')

@endsection
