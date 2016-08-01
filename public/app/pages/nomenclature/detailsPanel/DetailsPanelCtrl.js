/**
 * @author v.lugovksy
 * created on 16.12.2015
 */
(function () {
  'use strict';

  angular.module('BlurAdmin.pages.nomenclature')
      .controller('DetailsPanelCtrl', DetailsPanelCtrl);

  /** @ngInject */
  function DetailsPanelCtrl($scope, $stateParams) {
    $scope.showOrder = ($stateParams.id == null);
    $scope.order = null;
    $scope.list = [];
    $scope.loading = true;

    $scope.refreshData = function () {
      $scope.loading = true;
      if ($stateParams.id == null) {
        $scope.order = null;
      }
      $.ajax({
        url: ($stateParams.id == null) ? "/api/nomenclature" : "/api/nomenclature/get-by-order/" + $stateParams.id,
        type: 'POST',
        dataType: 'json',
        data: {data: null},
        success: function (data) {
          if (data.error) {
            console.log(data);
          } else {
            $scope.list = data.data;
            $scope.loading = false;
            if (data.order != null) {
              $scope.order = data.order;
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