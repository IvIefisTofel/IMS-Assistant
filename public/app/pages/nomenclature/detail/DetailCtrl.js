/**
 * @author v.lugovksy
 * created on 16.12.2015
 */
(function () {
  'use strict';

  angular.module('BlurAdmin.pages.nomenclature')
      .controller('DetailCtrl', DetailCtrl);

  /** @ngInject */
  function DetailCtrl($scope, $stateParams, $http, $window) {
    $scope.detail = {};
    $scope.loading = true;

    $scope.refreshData = function () {
      if ($stateParams.id == null) {
        $window.history.back();
      }
      $scope.loading = true;

      var $url = "/api/nomenclature/get/" + $stateParams.id;
      $http.post($url).then(function successCallback(response) {
        var data = response.data;
        if (data.error) {
          console.log(data);
        } else {
          $scope.detail = data;
          $scope.loading = false;
        }
      }, function errorCallback(response) {
        console.log(response.statusText);
      });
    };

    $scope.refreshData();
  }
})();