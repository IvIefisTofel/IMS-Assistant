(function () {
    'use strict';
    angular.module('BlurAdmin.pages.clients', [])
        .config(routeConfig);

    /** @ngInject */
    function routeConfig($stateProvider) {
        $stateProvider
            .state('clients', {
                url: '/',
                template: '<clients-list></clients-list>',
                title: 'Клиенты',
                sidebarMeta: {
                    icon: 'ion-ios-people',
                    order: 0
                }
            });
    }
})();