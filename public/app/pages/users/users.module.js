(function(){
  'use strict';
  angular.module('BlurAdmin.pages.users', [])
      .config(routeConfig);

  /** @ngInject */
  function routeConfig($stateProvider){
    $stateProvider
        .state('users', {
          url:         '/users',
          template:    '<users-list></users-list>',
          title:       'Пользователи',
          sidebarMeta: {
            permissionsRequired: true,
            icon:                'fa fa-users',
            order:               100
          }
        });
  }
})();