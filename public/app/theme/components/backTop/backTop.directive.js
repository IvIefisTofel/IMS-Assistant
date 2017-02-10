(function () {
  'use strict';
  angular.module('BlurAdmin.theme.components')
      .directive('backTop', backTop);

  /** @ngInject */
  function backTop() {
    return {
      restrict: 'E',
      templateUrl: 'app/theme/components/backTop/backTop.html',
      controller: function () {
        $('#backTop').backTop({
          'position': 200,
          'speed': 100
        });
      }
    };
  }
})();