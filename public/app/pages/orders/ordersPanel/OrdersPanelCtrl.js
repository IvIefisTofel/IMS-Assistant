/**
 * @author v.lugovksy
 * created on 16.12.2015
 */
(function () {
  'use strict';

  angular.module('BlurAdmin.pages.orders')
      .controller('OrdersPanelCtrl', OrdersPanelCtrl);

  /** @ngInject */
  function OrdersPanelCtrl($scope, $stateParams) {
    $scope.showClient = ($stateParams.id == null);
    $scope.clientName = null;
    $scope.list = [];
    $scope.loading = true;

    $scope.refreshData = function () {
      $scope.loading = true;
      if ($stateParams.id == null) {
        $scope.clientName = null;
      }
      $.ajax({
        url: ($stateParams.id == null) ? "/api/orders" : "/api/orders/get-by-client/" + $stateParams.id,
        type: 'POST',
        dataType: 'json',
        data: {data: null},
        success: function (data) {
          if (data.error) {
            console.log(data);
          } else {
            $scope.list = data.data;
            $scope.loading = false;
            if (data.client != null) {
              $scope.clientName = data.client.name;
            }
            $scope.$apply();
          }
        },
        error: function (data) {
          console.log(data);
        }
      });
    };

    $scope.refreshData();
  }
})();