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
                    order: 20
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
            .state('nomenclature-detail-edit', {
                url: '/orders/nomenclature/detail/{id:int}',
                params: {
                    id: {
                        value:  null,
                        squash: true
                    }
                },
                template: '<detail></detail>',
                title: 'Деталь'
            })
            .state('nomenclature-detail-add', {
                url: '/orders/nomenclature/detail/add',
                params: {
                    id: {
                        value:  'add',
                        squash: true
                    }
                },
                template: '<detail></detail>',
                title: 'Деталь'
            });
    }

})();
