(function () {
    'use strict';

    angular.module('BlurAdmin.pages.nomenclature', [])
        .config(routeConfig);

    /** @ngInject */
    function routeConfig($stateProvider) {
        $stateProvider
            .state('nomenclature', {
                url: '/orders/nomenclature',
                templateUrl: 'app/pages/nomenclature/nomenclature.html',
                title: 'Номенклатура',
                sidebarMeta: {
                    icon: 'fa fa-list',
                    order: 10
                }
            })
            .state('nomenclature-custom', {
                url: '/orders/nomenclature/{id:int}',
                params: {
                    id: {
                        value:  null,
                        squash: true
                    }
                },
                templateUrl: 'app/pages/nomenclature/nomenclature.html',
                title: 'Заказы'
            });
    }

})();
