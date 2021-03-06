@extends('layout.main')

@section('content')
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.8/angular.min.js"></script>
<script src="{{asset('resources/assets/js')}}/angularavel.js"></script>
<script>
app.controller('gridCtrl', function($scope){
  $scope.reqs = {!! $lists !!};
  $scope.reqs_disp = [].concat($scope.reqs);
});
</script>
<div class="container">
  <div class="row">

    <div class="jumbotron" align="center">
      <h1>Got a new customers list ?</h1>
      <p>Upload your new list now</p>
      <!-- @if(date('j') <= 28) -->
      <form method="post" enctype="multipart/form-data" action="" class="form-horizontal">
        <div class="form-group">
          <label for="lst" class="col-sm-2 control-label"> Your File : </label>
            <div class="col-sm-10">
              <input type="file" name="lst" id="lst" class="form-control">
            </div>
          </div>
          <div class="form-group">
            <label for="lstType" class="col-sm-2 control-label">List Type :</label>
            <div class="col-sm-10">
              <select id="lstType" name="lstType" class="form-control">
                <option value="Doctors"> Doctors List </option>
                <option value="Pharmacies"> Pharmacies List </option>
              </select>
            </div>
          </div>
          <input type="hidden" name="_token" value="{{csrf_token()}}">
          <button type="submit" class="btn btn-primary btn-lg">Upload List</button>
          <form>
          <!-- @endif -->

          </div>

          <table class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>#</th>
                <th>Company</th>
                <th>List Type</th>
                <th>Rep</th>
                <th>Uploaded Time</th>
                <th>Status</th>
              </tr>
              <tr>
                <th></th>
                <th>
                  <select class="form-control"  ng-model="search_company">
                    <option value="">- All -</option>
                    <option ng-repeat="row in reqs | unique_nested:'company'" value="[[row.company.name]]">[[row.company.name]]</option>
                  </select>
                </th>
                <th>
                  <select class="form-control"  ng-model="search_type">
                    <option value="">- All -</option>
                    <option ng-repeat="row in reqs | unique:'type'" value="[[row.type]]">[[row.type]]</option>
                  </select>
                </th>
                <th></th>
                <th>
                </th>
                <th>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr ng-repeat="row in reqs | filter:{company:{name: search_company}	, type: search_type,status: search_status}">
                <td id=l[[row.id]]>[[row.id]]</td>
                <td>[[row.company.name]]</td>
                <td>[[row.type]]</td>
                <td>[[row.user_name]]</td>
                <td>[[row.created_at]]</td>
                <td>[[row.status]]</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      @endsection
