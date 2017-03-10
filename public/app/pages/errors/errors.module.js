(function () {
    'use strict';
    angular.module('BlurAdmin.pages.errors', [])
        .config(routeConfig);

    /** @ngInject */
    function routeConfig($stateProvider) {
        $stateProvider
            .state('errors', {
                url: '/errors',
                template: '<errors-list></errors-list>',
                title: 'Лог ошибок',
                sidebarMeta: {
                    permissionsRequired: true,
                    icon: 'fa fa-bug',
                    order: 110
                }
            });
    }
})();