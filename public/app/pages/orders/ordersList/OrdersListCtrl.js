/**
 * @author v.lugovksy
 * created on 16.12.2015
 */
(function () {
  'use strict';

  angular.module('BlurAdmin.pages.orders')
      .controller('OrdersListCtrl', OrdersListCtrl);

  /** @ngInject */
  function OrdersListCtrl($scope, $stateParams, $http) {
    $scope.showClient = ($stateParams.id == null);
    $scope.clientName = null;
    $scope.defList = [];
    $scope.list = [];
    $scope.loading = true;

    $scope.filter = {1:true, 2:true, 3:false};
    $scope.propertyName = 'dateCreation';
    $scope.reverse = true;

    angular.element($('content-actions')).scope().refreshData('orders-list', [
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

    function noFilter(filterObj) {
      return Object.
      keys(filterObj).
      every(function (key) { return !filterObj[key]; });
    }

    $scope.filterBy = function(item) {
      return $scope.filter[item.statusCode] || noFilter($scope.filter);
    };

    $scope.refreshData = function () {
      $scope.loading = true;
      if ($stateParams.id == null) {
        $scope.clientName = null;
      }

      var $url = ($stateParams.id == null) ? "/api/orders" : "/api/orders/get-by-client/" + $stateParams.id;
      $http.post($url).then(function successCallback(response) {
        var data = response.data;
          if (data.error) {
            console.log(data);
          } else {
            $scope.list = data.data;
            $scope.loading = false;
            if (data.clientName != null) {
              $scope.clientName = data.clientName;
            }
          }
      }, function errorCallback(response) {
        console.log(response.statusText);
      });
    };

    $scope.refreshData();
  }
})();