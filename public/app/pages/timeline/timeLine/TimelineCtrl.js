(function () {
  'use strict';
  angular.module('BlurAdmin.pages.timeline')
      .controller('TimelineCtrl', TimelineCtrl);

  /** @ngInject */
  function TimelineCtrl($scope, $http) {
    $scope.list = [];
    $scope.loading = false;
    $scope.allData = false;

    $scope.refreshData = function(){
      $scope.loading = true;
      $http.post('api/events/' + $scope.list.length).then(function successCallback(response) {
        var data = response.data;
        if (data.error) {
          console.log(data);
        } else {
          for (var i = 0; i < data.length; i++) {
            data.date = new Date(data.date);
            $scope.list.push(data[i]);
          }
          $scope.loading = false;
          if (data.length < 10) {
            $scope.allData = true;
          }
          console.log($scope.list);
        }
      }, function errorCallback(response) {
        console.log(response.statusText);
      });
    };

    $scope.endPage = function (inview) {
      if (inview && !$scope.allData) {
        $scope.refreshData();
      }
    };
  }
})();