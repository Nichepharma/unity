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

  <div class='col-md-10 col-xs-12'>
    <h2>Supervisors Plan</h2>
    <table class="table myTable">
        <thead>
        <tr>
            <th><i class="glyphicon glyphicon-user blue"></i> Name: </th>
        </tr>
        </thead>
        <tbody>
          @foreach($users as $user)
          <tr>
            <th scope='row'>
              <a href="/unity/plan/user-plan/{{$company}}/{{$user->uid}}">{{$user->name}}</a>
            </th>
          </tr>
          @endforeach
        </tbody>
    </table>
  </div>

@endsection
