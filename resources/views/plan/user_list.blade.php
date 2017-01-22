@extends('layout.main')

@section('content')

    <?php
    if(isset($areas)){
      $list_data = array('items'=>$areas,
      'url'=>'plan/1',
      'headUrl'=>'plan/1',
      'title'=>'Regions');


      if(isset($regid)){
        $list_data['active'] = $regid;
      }
    ?>
    @include('layout.inner_left_menu', $list_data)
    <?php
    }
    ?>

  <div class='col-xs-12'>
    <h2>Supervisors Plan</h2>
    <table class="table myTable">
        <thead>
        <tr>
          <th colspan="3"><i class="glyphicon glyphicon-user blue"></i> Supervisor </th>
        </tr>
        </thead>
        <tbody>
          @foreach($users as $user)
          <tr>
            <th scope='row'>
              <a href="/unity/plan/user-plan/{{$company}}/{{$user->uid}}">{{$user->name}}</a>
            </th>
            <th><a href="/unity/plan/user-report/{{$company}}/{{$user->uid}}?t=plan"><i class="glyphicon glyphicon-list-alt blue"></i> Weekly Plan </a></th>
            <th><a href="/unity/plan/user-report/{{$company}}/{{$user->uid}}?t=report"><i class="glyphicon glyphicon-list-alt blue"></i> Weekly Report </a></th>
          </tr>
          @endforeach
        </tbody>
    </table>
  </div>

@endsection
