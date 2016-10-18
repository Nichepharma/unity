@extends('layout.main')

@section('content')
@include('others.print_buttons')

<div class="container-fluid page-content1">
              <h2><i class="glyphicon glyphicon-user"></i> {{ $userData[0]->name }} </h2>
              <input type="checkbox" name="chkActual" id="chkActual"> Show only actual visits and coaching sessions

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
                  filterPlan();
        });
  }
  function filterPlan(){
    if($('#chkActual').is(":checked")) {
      $('.alert-danger').hide();
    } else {
      $('.alert-danger').show();
    }
  }

  $('#chkActual').change(function(){
    filterPlan();
  });


  getPlan('{{date('Y-m-d', strtotime("last Saturday"))}}');
</script>
@endsection

@section('footer_inc')

@endsection
