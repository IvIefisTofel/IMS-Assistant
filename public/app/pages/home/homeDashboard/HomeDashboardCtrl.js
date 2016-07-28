/**
 * @author v.lugovksy
 * created on 16.12.2015
 */
(function () {
  'use strict';

  angular.module('BlurAdmin.pages.home')
      .controller('HomeDashboardCtrl', HomeDashboardCtrl);

  /** @ngInject */
  function HomeDashboardCtrl($scope) {
    $scope.list = [];
    $scope.current = null;
    $scope.client = {
      info: "Клиент не выбран.",
      files: null
    };

    $scope.showInfo = function(key){
      var item = $scope.list.filter(function( obj ) {
        return obj.id == key;
      });
      if (item.length > 0) {
        item = item[0];

        $scope.current = item.id;
        $scope.client.files = item.additions;

        if (item.description != null) {
          $scope.client.info = item.description;
        } else {
          $scope.client.info = "Нет информации.";
        }
      }
    };

    $.ajax({
      url: "/api/clients",
      type: 'POST',
      dataType: 'json',
      data: {data: null},
      success: function (data) {
        if (data.error) {
          console.log(data);
        } else {
          $scope.list = data.data;
          $scope.current = $scope.list[0].id;
          $scope.showInfo($scope.list[0].id);
          $scope.$apply();
        }
      },
      error: function (data) {
        console.log(data);
      }
    });
  }
})();