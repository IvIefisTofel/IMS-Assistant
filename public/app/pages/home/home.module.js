(function () {
    'use strict';

    angular.module('BlurAdmin.pages.home', [])
        .config(routeConfig);

    /** @ngInject */
    function routeConfig($stateProvider) {
        $stateProvider
            .state('home', {
                url: '/',
                templateUrl: 'app/pages/home/home.html',
                title: 'Клиенты',
                sidebarMeta: {
                    icon: 'ion-ios-people',
                    order: 0
                }
            });
    }

})();
