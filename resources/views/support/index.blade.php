@extends('layout.main')

@section('content')
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.8/angular.min.js"></script>
<div class="container">
  <div class="row">

    <div class="col-md-12" align="center">
      <div class="jumbotron" align="center">
        <h1>Have a technical problem ?</h1>
        <p>Start a technical support session now</p>
        <form>
        <div class="form-group">
          <select class="form-control" name="lstProblems" id="lstProblems">
            <option selected disabled>Problem Type</option>
            <option>Installing</option>
            <option>Login</option>
            <option>During Call</option>
            <option>Compatibility of customers list</option>
            <option>Feedback</option>
            <option>Synchronization</option>
            <option>Tacitapp.com</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary btn-lg">Start a new Ticket</button>
        <form>
      </div>
    </div>

    <div class="col-md-12"><br /></div>
    <table class="table table-striped table-bordered">
      <thead>
        <tr>
          <th>#</th>
          <th>Company</th>
          <th>Problem Type</th>
          <th>Date</th>
          <th>Status</th>
        </tr>
        <tr>
          <th></th>
          <th>
            <select class="form-control"  ng-model="search_company">
              <option value="">- All -</option>
              <option ng-repeat="row in reqs | unique:'company'" value="[[row.company.name]]">[[row.company.name]]</option>
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
            <select class="form-control"  ng-model="search_status">
              <option value="">- All -</option>
              <option ng-repeat="row in reqs | unique:'status'" value="[[row.status]]">[[row.status]]</option>
            </select>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr ng-repeat="row in reqs | filter:{company:{name: search_company}	, type: search_type,status: search_status}">
          <td><a href="request/2/[[row.id]]">[[row.id]]<a></td>
            <td>[[row.company.name]]</td>
            <td>[[row.type]]</td>
            <td>[[row.created_at]]</td>
            <td>[[row.status]]</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <script>
  var app=angular.module('myApp', []);

  app.config(function($interpolateProvider) {
    $interpolateProvider.startSymbol('[[');
    $interpolateProvider.endSymbol(']]');
  });

  app.filter('unique', function() {
    return function(collection, keyname) {
      var output = [],
      keys = [];

      angular.forEach(collection, function(item) {
        var key = item[keyname];
        if(keys.indexOf(key) === -1) {
          keys.push(key);
          output.push(item);
        }
      });

      return output;
    };
  });

  app.controller('gridCtrl', function($scope){
    $scope.reqs = {!! $reqs !!};
    $scope.reqs_disp = [].concat($scope.reqs);
  });
  </script>
  @endsection
