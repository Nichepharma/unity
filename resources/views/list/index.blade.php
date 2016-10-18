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
      <form method="post" enctype="multipart/form-data" action="">
        <div class="form-group">
          <input type="file" name="lst" id="lst">
        </div>
        <input type="hidden" name="_token" value="{{csrf_token()}}">
        <button type="submit" class="btn btn-primary btn-lg">Upload List</button>
        <form>
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
