// for this version of angular
// https://ajax.googleapis.com/ajax/libs/angularjs/1.5.8/angular.min.js
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

app.filter('unique_nested', function() {
  return function(collection, keyname) {
    var output = [],
    keys = [];

    angular.forEach(collection, function(item) {
      var key = item[keyname];
      // alert(key['id']);
      if(keys.indexOf(key['id']) === -1) {
        keys.push(key['id']);
        output.push(item);
      }
    });

    return output;
  };
});
