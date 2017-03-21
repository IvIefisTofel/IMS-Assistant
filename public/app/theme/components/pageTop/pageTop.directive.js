(function(){
  'use strict';
  angular.module('BlurAdmin.theme.components')
      .directive('pageTop', pageTop);

  /** @ngInject */
  function pageTop(){
    return {
      restrict:    'E',
      controller:  'PageTopCtrl',
      templateUrl: 'app/theme/components/pageTop/pageTop.html'
    };
  }
})();