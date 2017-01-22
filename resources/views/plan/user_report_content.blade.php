<?php
$from = $_GET["from"];

$weekDays = array(
  'Saturday' => $from,
  'Sunday' => date('Y-m-d', strtotime($from. ' + 1 days')),
  'Monday' => date('Y-m-d', strtotime($from. ' + 2 days')),
  'Tuesday' => date('Y-m-d', strtotime($from. ' + 3 days')),
  'Wednesday' => date('Y-m-d', strtotime($from. ' + 4 days')),
  'Thursday' => date('Y-m-d', strtotime($from. ' + 5 days')),
);

$previousWeek = date('Y-m-d', strtotime($from. ' - 7 days'));
$nextWeek = date('Y-m-d', strtotime($from. ' + 7 days'));

function printItem($item, $date, $time){
  if(($date != $item->date) || ($time != $item->time)){
    return '';
  }
  if ($item->type == 'kol'){
    $html = "<li class=\"alert alert-success\"> VIP visit to: {$item->name} ({$item->speciality})</li>";
  }elseif ($item->type == 'eval'){
    $html = "<li class=\"alert alert-info\"> Spotting visit to: {$item->name} (Drs={$item->v_doctors}, Ph={$item->v_pharms})</li>";

  }elseif ($item->type == 'plan_kol'){
    $html = "<li class=\"alert alert-success\"> VIP visit to: {$item->name} ({$item->speciality})</li>";
  }elseif ($item->type == 'plan_eval'){
    $html = "<li class=\"alert alert-info\"> Spotting visit to: {$item->name}</li>";
  }
  return $html;
}

?>
<div class="row">
  From: ({{$from}}) To ({{$weekDays['Thursday']}})
</div>
<div class="row">
<button class="btn btn-default pull-left glyphicon glyphicon-arrow-left" onclick="getPlan('{{$previousWeek}}')"></button>
<button class="btn btn-default pull-right glyphicon glyphicon-arrow-right" onclick="getPlan('{{$nextWeek}}')"></button>
<br>
<br>
<table class="table myTable" border="1">
    <thead>
    <tr>
      <th class="plantableheadclass"></th>
      <th class="plantableheadclass">A.M</th>
      <th class="plantableheadclass">P.M</th>
    </tr>
    </thead>
    <tbody>
      @foreach($weekDays as $key => $val)
      <tr>
      <td>{{$key . ' ' . $val}}</td>
      <td>
        <ol class="customers_plan">
          <?php
          foreach ($results as $result) {
            echo printItem($result, $val, 'AM');
          }
          ?>
        </ol>
      </td>
      <td>
        <ol class="customers_plan">
          <?php
          foreach ($results as $result) {
            echo printItem($result, $val, 'PM');
          }
          ?>
        </ol>
      </td>
      </tr>
      @endforeach
    </tbody>
</table>
</div>
