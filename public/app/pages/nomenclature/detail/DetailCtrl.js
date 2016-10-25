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
    function isEmpty(obj) {
      if (obj == null) return true;
      if (obj.length > 0)    return false;
      if (obj.length === 0)  return true;
      if (typeof obj !== "object") return true;
      for (var key in obj) {
        if (Object.prototype.hasOwnProperty.call(obj, key)) return false;
      }

      return true;
    }

    $scope.detail = {};
    $scope.orders  = [];
    $scope.loading = true;

    $scope.selected = {
      client: null,
      order: null
    };

    $scope.filterBy = function(item) {
      if (!isEmpty($scope.selected.client)) {
        return item.clientId == $scope.selected.client.key;
      } else {
        return true;
      }
    };
    
    $scope.clear = function(){
      $scope.selected.client = undefined;
      $scope.selected.order = undefined;
    };

    $scope.changeClient = function() {
      if (!isEmpty($scope.selected.client)) {
        $scope.selected.order = undefined;
      }
    };

    $scope.changeOrder = function() {
      if (!isEmpty($scope.selected.order)) {
        $scope.selected.client = {key: $scope.clients[$scope.selected.order.clientId].id, value: $scope.clients[$scope.selected.order.clientId]};
      }
    };

    $scope.refreshData = function () {
      if ($stateParams.id == null) {
        $window.history.back();
      }
      $scope.loading = true;

      var $url = "/api/nomenclature/get-with-parents/" + $stateParams.id;
      $http.post($url).then(function successCallback(response) {
        var data = response.data;
        if (data.error) {
          console.log(data);
        } else {
          $scope.detail = data.data[0];
          $scope.clients = data.clients;
          angular.forEach($scope.clients, function(client, key) {
            angular.forEach(client.orders, function(order, key) {
              if ($scope.detail.orderId == order.id) {
                $scope.selected.client = {key: client.id, value: client};
                $scope.selected.order = order;
              }
              $scope.orders = $scope.orders.concat(order);
            });
          });
          $scope.loading = false;
        }
      }, function errorCallback(response) {
        console.log(response.statusText);
      });
    };

    $scope.refreshData();

    $scope.test = function(){
      $scope.selected.client = null;
      // console.log($scope.getClients());
      // console.log(isEmpty($scope.selected.client));
    };
  }
})();