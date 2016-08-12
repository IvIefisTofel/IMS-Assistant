/**
 * @author v.lugovksy
 * created on 16.12.2015
 */
(function () {
  'use strict';

  angular.module('BlurAdmin.theme.components')
      .directive('contentActions', contentActions);

  /** @ngInject */
  function contentActions() {
    return {
      restrict: 'E',
      controller: 'ContentActionsCtrl',
      templateUrl: 'app/theme/components/contentTop/contentActions/contentActions.html'
    };
  }
})();