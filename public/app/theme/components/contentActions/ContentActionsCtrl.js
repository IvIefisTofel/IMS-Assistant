(function () {
  'use strict';
  angular.module('BlurAdmin.theme.components')
      .controller('ContentActionsCtrl', ContentActionsCtrl);

  /** @ngInject */
  function ContentActionsCtrl($scope) {
    $scope.showActions = $scope.actions != undefined;
      
    $scope.action = function(id) {
      if ($scope.actions[id].action !== undefined && $scope.actions[id].action !== null) {
        if ($scope.actions[id].params !== undefined && $scope.actions[id].params !== null) {
          $scope[$scope.actions[id].action]($scope.actions[id].params);
        } else {
          $scope[$scope.actions[id].action]();
        }
      }
    };
  }
})();