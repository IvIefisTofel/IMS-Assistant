/**
 * @author v.lugovksy
 * created on 16.12.2015
 */
(function () {
  'use strict';

  angular.module('BlurAdmin.theme.components')
      .controller('MsgCenterCtrl', MsgCenterCtrl);

  /** @ngInject */
  function MsgCenterCtrl($scope, $http, $interval) {
    $scope.notifications = [];


    $scope.update = function(){
        $http.post('/api/services/notifications', null).then(function successCallback(response) {
            var data = response.data;
            if (data.error) {
                console.log(data);
            } else {
                $scope.notifications = data.hotOrders;
            }
        }, function errorCallback(response) {
            console.log(response.statusText);
        });
    };

    $scope.update();
    $interval(function() {
        $scope.update();
    }, 60000*5);
  }
})();