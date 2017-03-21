(function(){
  'use strict';
  angular.module('BlurAdmin.pages.timeline', [])
      .config(routeConfig);

  /** @ngInject */
  function routeConfig($stateProvider){
    $stateProvider
        .state('timeline', {
          url:         '/timeline',
          template:    '<time-line></time-line>',
          title:       'Список изменений',
          sidebarMeta: {
            permissionsRequired: true,
            icon:                'fa fa-clock-o',
            order:               110
          }
        });
  }
})();