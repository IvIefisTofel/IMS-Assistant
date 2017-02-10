(function () {
  'use strict';
  angular.module('BlurAdmin.pages.clients')
      .directive('clientsList', clientsList);

  /** @ngInject */
  function clientsList() {
    return {
      restrict: 'E',
      controller: 'ClientsListCtrl',
      templateUrl: 'app/pages/clients/clientsList/clientsList.html'
    };
  }
})();