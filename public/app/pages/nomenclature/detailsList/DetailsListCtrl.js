/**
 * @author v.lugovksy
 * created on 16.12.2015
 */
(function () {
  'use strict';

  angular.module('BlurAdmin.pages.nomenclature')
      .controller('DetailsListCtrl', DetailsListCtrl);

  /** @ngInject */
  function DetailsListCtrl($scope, $stateParams, $http) {
    $scope.showOrder = ($stateParams.id == null);
    $scope.order = null;
    $scope.list = [];
    $scope.loading = true;
    $scope.propertyName = 'dateCreation';
    $scope.reverse = true;

    angular.element($('content-actions')).scope().refreshData('details-list', [
      {
        text: "Обновить",
        class: "btn-info",
        iconClass: "fa fa-refresh",
        action: 'refreshData'
      }
    ]);

    $scope.sortBy = function(propertyName) {
      if (propertyName.indexOf('date') != -1) {
        $scope.reverse = ($scope.propertyName === propertyName) ? !$scope.reverse : true;
      }
      else {
        $scope.reverse = ($scope.propertyName === propertyName) ? !$scope.reverse : false;
      }
      $scope.propertyName = propertyName;
    };

    $scope.refreshData = function () {
      $scope.loading = true;
      if ($stateParams.id == null) {
        $scope.order = null;
      }

      var $url = ($stateParams.id == null) ? "/api/nomenclature" : "/api/nomenclature/get-by-order/" + $stateParams.id;
      $http.post($url).then(function successCallback(response) {
        var data = response.data;
        if (data.error) {
          console.log(data);
        } else {
          $scope.list = data.data;
          $scope.loading = false;
          if (data.order != null) {
            $scope.order = data.order;
          }
        }
      }, function errorCallback(response) {
        console.log(response.statusText);
      });
    };

    $scope.refreshData();
  }
})();