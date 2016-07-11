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
                title: 'Главная',
                sidebarMeta: {
                    icon: 'ion-android-home',
                    order: 0,
                },
            });
    }

})();
