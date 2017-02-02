/**
 * @author v.lugovsky
 * created on 16.12.2015
 */
(function () {
  'use strict';

  angular.module('BlurAdmin.pages', [
    'ui.router',
    'BlurAdmin.pages.clients',
    'BlurAdmin.pages.orders',
    'BlurAdmin.pages.nomenclature',
    'BlurAdmin.pages.users'
  ]).config(routeConfig);

  /** @ngInject */
  function routeConfig($urlRouterProvider) {
    $urlRouterProvider.otherwise('/');
  }

})();
