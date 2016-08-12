(function () {
    'use strict';

    angular.module('BlurAdmin.pages.clients', [])
        .config(routeConfig);

    /** @ngInject */
    function routeConfig($stateProvider) {
        $stateProvider
            .state('clients', {
                url: '/',
                templateUrl: 'app/pages/clients/clients.html',
                title: 'Клиенты',
                sidebarMeta: {
                    icon: 'ion-ios-people',
                    order: 0
                }
            });
    }

})();
