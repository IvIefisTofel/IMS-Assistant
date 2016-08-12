/**
 * @author v.lugovksy
 * created on 16.12.2015
 */
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