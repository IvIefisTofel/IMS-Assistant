(function () {
  'use strict';
  angular.module('BlurAdmin.pages', [
    'ui.router',
    'BlurAdmin.pages.clients',
    'BlurAdmin.pages.orders',
    'BlurAdmin.pages.nomenclature',
    'BlurAdmin.pages.users',
    'BlurAdmin.pages.timeline',
    'BlurAdmin.pages.errors'
  ]).config(routeConfig);

  /** @ngInject */
  function routeConfig($urlRouterProvider) {
    $urlRouterProvider.otherwise('/');
  }
})();