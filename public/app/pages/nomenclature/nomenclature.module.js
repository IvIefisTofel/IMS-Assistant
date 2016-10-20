(function () {
    'use strict';

    angular.module('BlurAdmin.pages.nomenclature', [])
        .config(routeConfig);

    /** @ngInject */
    function routeConfig($stateProvider) {
        $stateProvider
            .state('nomenclature', {
                url: '/orders/nomenclature',
                template: '<details-list></details-list>',
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
                template: '<details-list></details-list>',
                title: 'Номенклатура'
            })
            .state('nomenclature-detail', {
                url: '/orders/nomenclature/detail/{id:int}',
                params: {
                    id: {
                        value:  null,
                        squash: true
                    }
                },
                template: '<detail></detail>',
                title: 'Деталь'
            });
    }

})();
