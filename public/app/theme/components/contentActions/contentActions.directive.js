(function(){
  'use strict';
  angular.module('BlurAdmin.theme.components')
      .directive('contentActions', contentActions);

  /** @ngInject */
  function contentActions(){
    return {
      restrict:    'E',
      controller:  'ContentActionsCtrl',
      templateUrl: 'app/theme/components/contentActions/contentActions.html'
    };
  }
})();