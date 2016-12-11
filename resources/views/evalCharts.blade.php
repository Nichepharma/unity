@extends('layout.main')

@section('content')

<style>
/* centered columns styles */
.row-centered {
    text-align:center;
    margin-bottom: 50px;
}
.col-centered {
    display:inline-block;
    float:none;
    /* reset the text-align */
    text-align:left;
    /* inline-block space fix */
    margin-right:-4px;
}
</style>
<div class="container">
  @include('others.print_buttons')

  <div class="row row-centered">
      <form class="form-inline">
        <div class="form-group">
          <label>Country : </label>
          <select class="form-control" id="country" onchange="getSupersAndReps();">
            <option selected disabled>Choose Here</option>
            @foreach($countries as $country)
            <option value="{{$country->regid}}">{{$country->region}}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>Supervisor : </label>
          <select class="form-control" id="supervisor">
            <option selected disabled>Choose Here</option>
          </select>
          <label>Sales Rep : </label>
          <select class="form-control" id="rep">
            <option selected disabled>Choose Here</option>
          </select>
        </div>
        <div class="form-group">
          <label>When : </label>
          <select class="form-control" id="year">
            <option selected disabled>Choose here</option>
            <option value="2016">2016</option>
          </select>
          <select class="form-control" id="month">
            <option selected disabled>Choose here</option>
            <option value="1">January</option>
            <option value="2">February</option>
            <option value="3">March</option>
            <option value="4">April</option>
            <option value="5">May</option>
            <option value="6">June</option>
            <option value="7">July</option>
            <option value="8">August</option>
            <option value="9">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
          </select>
        </div>
        <input type="button" class="btn btn-primary" onclick="preview()" value="Preview">

      </form>
  </div>
  <!-- <div class="row row-centered">
    <ol class="breadcrumb">
      <li><a href="#">Home</a></li>
      <li><a href="#">Library</a></li>
      <li class="active">Data</li>
    </ol>
    <ol class="breadcrumb">
      <li class="active">2016</li>
      <li class="active">06</li>
    </ol>
  </div> -->
  <div class="row">
    <div class="col-md-10 col-md-offset-1">

      @if(isset($cats_data))
      @foreach($cats_data as $cat_data)
      <div class="row">
        <div class="col-md-6">
          <div class="panel panel-primary">
            <div class="panel-heading">
              <h3 class="panel-title">{{$cat_data->name}} skill at a glance</h3>
            </div>
            <div class="panel-body">
              <dl class="dl-horizontal">
                <dt>Need Improvments</dt><dd> {{$cat_data->nis}} </dd>
                <dt>Good</dt><dd> {{$cat_data->goods}} </dd>

                <dt>Need Improvments (%)</dt><dd> {{round($cat_data->nis * 100 / $cat_data->totals)}}% </dd>
                <dt>Good(%) </dt><dd> {{round($cat_data->goods * 100 / $cat_data->totals)}}% </dd>

                <dt>Total Answers</dt><dd> {{$cat_data->totals}} </dd>

              </dl>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <canvas id="myChart{{$cat_data->cat_id}}"></canvas>
        </div>
      </div>
      @endforeach
      @endif
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.4/Chart.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.0/angular.min.js"></script>
<script>
var option = {
  animation: {
    duration:5000
  },
  responsive:false,
  maintainAspectRatio: false
};

@if(isset($cats_data))
@foreach($cats_data as $cat_data)
var ctx = document.getElementById("myChart{{$cat_data->cat_id}}").getContext('2d');

var myChart = new Chart(ctx, {
  type: 'pie',
  option : option,
  data: {
    labels: ["Need Improvment", "Good"],
    datasets: [{
      backgroundColor: [
        "#34495e",
        "#2ecc71"
      ],
      data: [{{$cat_data->nis}}, {{$cat_data->goods}}]
    }]
  }
});
@endforeach
@endif
</script>

<script>
  var myApp = angular.module('myApp', []);
  myApp.config(function ($interpolateProvider) {
      $interpolateProvider.startSymbol('[[');
      $interpolateProvider.endSymbol(']]');
  });
  myApp.controller('gridCtrl', ['$scope', '$http', function($scope, $http) {
    $scope.getSupersAndReps = function(regID){
        $http({
        method: 'GET',
        url: 'http://tacitapp.com/unity/eval-charts/1?ajax=1&region=' + regID
      }).then(function successCallback(response) {
          $("#supervisor").replaceOptions(response.data.sups);
          $("#rep").replaceOptions(response.data.reps);
        }, function errorCallback(response) {
          alert('Ajax Connection Error!');
        });
    }
  }]);
  </script>

<script>
function preview(){
  window.location = '1?region=' + $("#country" ).val() + '&year=' + $("#year" ).val() + '&month=' + $("#month" ).val()
   + '&supervisor=' + $("#supervisor" ).val() + '&rep=' + $("#rep" ).val();
}

function getSupersAndReps(){
var regID = $("#country").val();
var scope = angular.element(document.getElementsByTagName("body")[0]).scope();
scope.$apply(function () {
  scope.getSupersAndReps(regID);
});

}

(function($, window) {
  $.fn.replaceOptions = function(options) {
    var self, $option;

    this.empty();
    self = this;

    $option = $("<option selected disabled>Choose Here</option>");
    self.append($option);
    $.each(options, function(index, option) {
      $option = $("<option></option>")
        .attr("value", option.value)
        .text(option.text);
      self.append($option);
    });
  };
})(jQuery, window);

</script>

@endsection
