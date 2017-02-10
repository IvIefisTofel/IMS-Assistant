(function () {
    'use strict';
    angular.module('BlurAdmin.pages.orders', [])
        .config(routeConfig);

    /** @ngInject */
    function routeConfig($stateProvider) {
        $stateProvider
            .state('orders', {
                url: '/orders',
                template: '<orders-list></orders-list>',
                title: 'Заказы',
                sidebarMeta: {
                    icon: 'fa fa-th-list',
                    order: 10
                }
            })
            .state('orders-custom', {
                url: '/orders/{id:int}',
                params: {
                    id: {
                        value:  null,
                        squash: true
                    }
                },
                template: '<orders-list></orders-list>',
                title: 'Заказы'
            });
    }
})();