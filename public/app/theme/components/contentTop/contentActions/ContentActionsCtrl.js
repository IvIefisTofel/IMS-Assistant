/**
 * @author v.lugovksy
 * created on 16.12.2015
 */
(function () {
  'use strict';

  angular.module('BlurAdmin.theme.components')
      .controller('ContentActionsCtrl', ContentActionsCtrl);

  /** @ngInject */
  function ContentActionsCtrl($scope, $rootScope, $timeout) {
    var $element = null;

    $scope.showActions = false;
    $scope.buttons = [];

    $rootScope.$on('$stateChangeStart', function(){
      $scope.showActions = false;
    });

    $scope.refreshData = function(element, buttons) {
      if (buttons.length > 0) {
        $scope.showActions = true;

        $element = element;
        $scope.buttons = [];
        $timeout(function(){
          $scope.buttons = buttons;
        }, 170);
      }
    };

    $scope.action = function(id) {
      if ($element !== null && $scope.buttons[id].action !== undefined && $scope.buttons[id].action !== null) {
        if ($scope.buttons[id].params !== undefined && $scope.buttons[id].params !== null) {
          angular.element($($element)).scope()[$scope.buttons[id].action]($scope.buttons[id].params);
        } else {
          angular.element($($element)).scope()[$scope.buttons[id].action]();
        }
      }
    };
  }
})();