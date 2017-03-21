(function(){
  'use strict';
  angular.module('BlurAdmin.pages.clients', [])
      .config(routeConfig);

  /** @ngInject */
  function routeConfig($stateProvider){
    $stateProvider
        .state('clients', {
          url:         '/',
          template:    '<clients-list></clients-list>',
          title:       'Клиенты',
          sidebarMeta: {
            icon:  'fa fa-handshake-o',
            order: 0
          }
        });
  }
})();