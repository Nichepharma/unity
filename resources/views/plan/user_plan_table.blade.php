<?php
  function arrayGroupBy($arr, $field)
  {
      $result = array();
      foreach ($arr as $data) {
          $id = $data->$field;
          if (isset($result[$id])) {
              $result[$id][] = $data;
          } else {
              $result[$id] = array($data);
          }
      }
      return $result;
  }

  $weekDays = array(
    'saturday' => $from,
    'sunday' => date('Y-m-d', strtotime($from. ' + 1 days')),
    'monday' => date('Y-m-d', strtotime($from. ' + 2 days')),
    'tuesday' => date('Y-m-d', strtotime($from. ' + 3 days')),
    'wednesday' => date('Y-m-d', strtotime($from. ' + 4 days')),
    'thursday' => date('Y-m-d', strtotime($from. ' + 5 days')),
  );

  $previousWeek = date('Y-m-d', strtotime($from. ' - 7 days'));
  $nextWeek = date('Y-m-d', strtotime($from. ' + 7 days'));

  if(isset($plan[1]))
  $doctorsPlan = arrayGroupBy($plan[1],'date');

  if(isset($visits[1]))
  $doctorsVisit = arrayGroupBy($visits[1],'date');

  if(isset($plan_eval))
  $evalsPlan = arrayGroupBy($plan_eval,'date');

  if(isset($visits_eval))
  $evalsVisit = arrayGroupBy($visits_eval,'date');
?>

<button class="btn btn-default pull-left glyphicon glyphicon-arrow-left" onclick="getPlan('{{$previousWeek}}')"></button>
<button class="btn btn-default pull-right glyphicon glyphicon-arrow-right" onclick="getPlan('{{$nextWeek}}')"></button>
<br>
<br>
<table class="table myTable" border="1">
    <thead>
    <tr>
        <th class="plantableheadclass">Types</th>
        @foreach($weekDays as $day)
            <th class="plantableheadclass">{{ date('l F d, Y', strtotime($day)) }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
      <tr>
          <th class="plantableheadclass">KOL Visits</th>
          @foreach($weekDays as $day)
              <td>
                @if(isset($doctorsPlan[$day]))
                <ol class="customers_plan">
                  @foreach($doctorsPlan[$day] as $doctor)
                  <li class="alert alert-{{$doctor->visited}}">
                    {{$doctor->name}} ({{$doctor->speciality}})
                  </li>
                  @endforeach
                </ol>
                @endif

                @if(isset($doctorsVisit[$day]))
                  <ol class="customers_plan">
                    @foreach($doctorsVisit[$day] as $doctor)
                      <li class="alert alert-info">
                        {{$doctor->name}} ({{$doctor->speciality}})
                      </li>
                    @endforeach
                  </ol>
                @endif
              </td>
          @endforeach
      </tr>
      <tr>
          <th class="plantableheadclass">Coaching Sessions</th>
          @foreach($weekDays as $day)
              <td>
                @if(isset($evalsPlan[$day]))
                  <ol class="customers_plan">
                    @foreach($evalsPlan[$day] as $rep)
                      <li class="alert alert-{{$rep->visited}}">
                        {{$rep->name}}
                      </li>
                    @endforeach
                  </ol>
                @endif

                @if(isset($evalsVisit[$day]))
                  <ol class="customers_plan">
                    @foreach($evalsVisit[$day] as $rep)
                      <li class="alert alert-info">
                        {{$rep->name}}
                      </li>
                    @endforeach
                  </ol>
                @endif
              </td>
          @endforeach
      </tr>
    </tbody>
</table>
