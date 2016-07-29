(function () {
    'use strict';

    angular.module('BlurAdmin.pages.orders', [])
        .config(routeConfig);

    /** @ngInject */
    function routeConfig($stateProvider) {
        $stateProvider
            .state('orders', {
                url: '/orders/:id',
                params: {
                    id: {
                        value:  null,
                        squash: true
                    }
                },
                templateUrl: 'app/pages/orders/orders.html',
                title: 'Заказы',
                fixedHref: '/orders',
                sidebarMeta: {
                    icon: 'ion-clipboard',
                    order: 10
                }
            });
    }

})();
