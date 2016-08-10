@extends('layout.main')

@section('content')
<div class="container">
  @include('others.print_buttons')

    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">

                <div class="panel-body">
                  <table class="table">
                    <thead>
                    <tr>
                        <th>Category</th>
                        <th>Point</th>
                        <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $tmp_cat="Nothing"; ?>
                    @foreach ($eval_ans as $eval_answer)
                    <tr>
                      <?php
                        if(($eval_answer->cat != $tmp_cat) || ($tmp_cat=='Nothing')){
                          echo "<td>{$eval_answer->cat}</td>";
                        }else{
                          echo "<td></td>";
                        }
                        $tmp_cat = $eval_answer->cat;
                      ?>
                      <td>{{ $eval_answer->name }}</td>
                      <?php
                        if ($eval_answer->cat != 'OTHER'){
                          $answers_ids = array("1", "2", "3", "4", "5");
                          $answers_names = array("Needs Improvement", "Fair", "Good", "Very Good", "Excellent");
                          $eval_answer->answer = str_replace($answers_ids, $answers_names, $eval_answer->answer);
                        }
                      ?>
                      <td>{{ $eval_answer->answer }}</td>
                    </tr>
                    @endforeach
                  </tbody>
                  </table>
                  @if ($company ==2)
                  <table class="table">
                    <thead>
                    <tr>
                        <th>Grand Total</th>
                        <th>Final Grade</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($eval_total as $eval_total_data)
                    <tr>
                      <td>{{$eval_total_data->c}}</td>
                      <td>50</td>
                    </tr>
                    @endforeach
                  </tbody>
                  </table>
                  @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
