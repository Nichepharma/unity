@extends('layout.main')

@section('content')
<div class="container">
  @include('others.print_buttons')

    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <h2><i class="glyphicon glyphicon-user"></i> {{ $userData[0]->name }} </h2>
      </div>
    </div>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">

                <div class="panel-body">
                  <table class="table">
                    <thead>
                      <tr>
                        <td>Supervisor Signature</td>
                        <td>Representative Signature</td>
                        <td>Date</td>
                        <td>Details</td>
                      </tr>
                    </thead>
                    <tbody>
                    @foreach ($eval_sessions as $eval_session)
                    <tr>
                      <td>{{ $eval_session->supervisor_signature }}</td>
                      <td>{{ $eval_session->rep_signature }}</td>
                      <td>{{ $eval_session->date }}</td>
                      <td><a href={{url('eval/' . $company . '/' . $eval_session->id)}}> Evalation Sheet </a></td>
                    </tr>
                    @endforeach
                  </tbody>
                  </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
