/**
 * @author v.lugovksy
 * created on 16.12.2015
 */
(function () {
    'use strict';

    angular.module('BlurAdmin.pages.nomenclature')
        .controller('DetailsListCtrl', DetailsListCtrl);

    /** @ngInject */
    function DetailsListCtrl($scope, $state, $stateParams, $http, $filter, $timeout, $uibModal) {
        $scope.showOrder = ($stateParams.id == null);
        $scope.order = null;
        $scope.list = [];
        $scope.loading = true;

        $scope.filter = {true: true, false: true};
        $scope.propertyName = 'dateCreation';
        $scope.reverse = true;

        var tree;
        $scope.treeControl = tree = {};
        $scope._filter = {};

        $scope.actionVariants = {
            openGallery: {
                text: "Все чертежи",
                class: "btn-success",
                iconClass: "fa fa-picture-o",
                action: 'orderGallery'
            },
            default: [
                {
                    text: "Добавить",
                    class: "btn-primary",
                    iconClass: "fa fa-plus",
                    action: 'addDetail'
                },
                {
                    text: "Обновить",
                    class: "btn-info",
                    iconClass: "fa fa-refresh",
                    action: 'refreshData'
                }
            ]
        };
        $scope.actions = $scope.actionVariants.default;

        $scope.sortBy = function (propertyName) {
            if (propertyName.indexOf('date') != -1) {
                $scope.reverse = ($scope.propertyName === propertyName) ? !$scope.reverse : true;
            }
            else {
                $scope.reverse = ($scope.propertyName === propertyName) ? !$scope.reverse : false;
            }
            $scope.propertyName = propertyName;
            $scope.list = $filter('orderBy')($scope.list, $scope.propertyName, $scope.reverse);
        };

        function noFilter(filterObj) {
            return Object.keys(filterObj).every(function (key) {
                return !filterObj[key];
            });
        }

        $scope.filterBy = function (item) {
            return $scope.filter[item.status] || noFilter($scope.filter);
        };

        $scope.expanding_property = {
            field: 'name',
            titleTemplate: '<button class="btn btn-link" ng-click="sortBy(\'name\')">{{expandingProperty.displayName || expandingProperty.field}}</button>' +
                '<span class="sortorder" ng-show="propertyName === \'name\'" ng-class="{reverse: reverse}"></span>',
            firstStatus: true,
            displayName: 'Имя детали'
        };

        var filesTemplate = function () {
            return '<span ng-if="node.__children__.length == 0 && node[col.field].length == null"> --- </span>' +
                '<a ng-if="node[col.field].length == 1" class="btn btn-primary btn-xs" href="/files/{{node[col.field][0].id}}/{{node[col.field][0].name}}" download="">' +
                    '{{node[col.field][0].name}}' +
                '</a>' +
                '<div ng-if="node[col.field].length > 1" class="dropdown">' +
                    '<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">Файлы<span class="caret"></span></button>' +
                    '<ul class="dropdown-menu">' +
                        '<li ng-repeat="file in node[col.field]"><a href="/files/{{file.id}}/{{file.name}}" download="">{{file.name}}</a></li>' +
                    '</ul>' +
                '</div>';
        };

        var imagesTemplate = function () {
            return '<span ng-if="node.__children__.length == 0 && node[col.field].length == null"> --- </span>' +
                '<a ng-if="node[col.field].length == 1" class="btn btn-primary btn-xs" href ng-click="openGallery(node.id)">' +
                    '{{node[col.field][0].name}}' +
                '</a>' +
                '<div ng-if="node[col.field].length > 1" class="dropdown">' +
                    '<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">Файлы<span class="caret"></span></button>' +
                    '<ul class="dropdown-menu">' +
                        '<li ng-repeat="(key, file) in node[col.field]"><a href ng-click="openGallery(node.id, key)">{{file.name}}</a></li>' +
                    '</ul>' +
                '</div>';
        };

        $scope.defaults = [{
            field: 'code',
            titleTemplate: '<button class="btn btn-link" ng-click="sortBy(\'code\')">{{col.displayName || col.field}}</button>' +
                '<span class="sortorder" ng-show="propertyName === \'code\'" ng-class="{reverse: reverse}"></span>',
            colspan: "node.__children__.length > 0 ? 0 : 1",
            displayName: 'Шифр детали'
        }, {
            field: 'orderCode',
            titleTemplate: '<button class="btn btn-link" ng-click="sortBy(\'orderCode\')">{{col.displayName || col.field}}</button>' +
                '<span class="sortorder" ng-show="propertyName === \'orderCode\'" ng-class="{reverse: reverse}"></span>',
            colspan: "node.__children__.length > 0 ? 2 : 1",
            displayName: 'Шифр заказа'
        }, {
            field: 'pattern',
            titleTemplate: '<button class="btn btn-link" disabled="disabled">{{col.displayName || col.field}}</button>',
            cellTemplate: imagesTemplate(),
            displayName: 'Чертеж'
        }, {
            field: 'model',
            titleTemplate: '<button class="btn btn-link" disabled="disabled">{{col.displayName || col.field}}</button>',
            cellTemplate: filesTemplate(),
            displayName: 'Модель'
        }, {
            field: 'project',
            titleTemplate: '<button class="btn btn-link" disabled="disabled">{{col.displayName || col.field}}</button>',
            cellTemplate: filesTemplate(),
            displayName: 'Проект'
        }, {
            field: 'dateCreation',
            titleTemplate: '<button class="btn btn-link" ng-click="sortBy(\'dateCreation\')">{{col.displayName || col.field}}</button>' +
                '<span class="sortorder" ng-show="propertyName === \'dateCreation\'" ng-class="{reverse: reverse}"></span>',
            cellTemplate: '<div>{{node.dateCreation ? (node.dateCreation | date: "dd MMMM yyyy, EEEE") : "---"}}</div>',
            displayName: 'Дата создания'
        }, {
            field: 'dateEnd',
            titleTemplate: '<button class="btn btn-link" ng-click="sortBy(\'dateEnd\')">{{col.displayName || col.field}}</button>' +
                '<span class="sortorder" ng-show="propertyName === \'dateEnd\'" ng-class="{reverse: reverse}"></span>',
            cellTemplate: '<div>{{node.dateEnd ? (node.dateEnd | date: "dd MMMM yyyy, EEEE") : "---"}}</div>',
            displayName: 'Дата исполнения'
        }, {
            cellTemplate: '<a ng-if="node.id" ui-sref="nomenclature-detail-edit({id: node.id})" class="btn btn-xs btn-primary">Деталь</a>'
            // cellTemplate: '<button ng-click="test(node)">Деталь</button>'
        }];

        $scope.addDetail = function () {
            $state.go('nomenclature-detail-add');
        };

        $scope.showGallery = false;
        $scope.galleries = [];
        $scope.gallery = {
            images: null,
            methods: {}
        };
        $scope.openGallery = function (id, index) {
            if (index == undefined) {
                index = 0;
            }
            if ($scope.galleries[id] != undefined) {
                $scope.gallery.images = $scope.galleries[id];
                $scope.gallery.methods.open(index);
            }
        };

        function addGalleryItem(item, addMain) {
            angular.forEach(item.pattern, function (image, key) {
                if (addMain) {
                    $scope.galleries.main.push({
                        image: {
                            id: image.id,
                            name: image.name
                        },
                        url: '/files/h/950/' + image.id + '/' + image.name,
                        extUrl: '/files/' + image.id + '/' + image.name,
                        desText: item.code
                    });
                }
                $scope.galleries[item.id].push({
                    url: '/files/h/950/' + image.id + '/' + image.name,
                    extUrl: '/files/' + image.id + '/' + image.name
                });
            });
        }

        var modalInstance = null;
        $scope.modalActions = {
            open: function () {
                modalInstance = $uibModal.open({
                    animation: true,
                    templateUrl: 'app/pages/nomenclature/modal/orderGallery.html',
                    size: 'exlg',
                    backdrop: 'static',
                    scope: $scope
                });
            },
            close: function () {
                modalInstance.dismiss();
                modalInstance = null;
            }
        };
        $scope.orderGallery = function(){
            $scope.modalActions.open();
        };

        $scope.refreshData = function () {
            $scope.loading = true;
            if ($stateParams.id == null) {
                $scope.order = null;
            }

            var $url = ($stateParams.id == null) ? "/api/nomenclature/tree" : "/api/nomenclature/get-by-order-tree/" + $stateParams.id;
            $http.post($url).then(function successCallback(response) {
                var data = response.data;
                if (data.error) {
                    console.log(data);
                } else {
                    $scope.list = $filter('orderBy')(data.data, $scope.propertyName, $scope.reverse);
                    $scope.loading = false;
                    $scope.galleries = [];
                    if (data.order != null) {
                        $scope.galleries.main = [];
                        $scope.order = data.order;
                    }
                    angular.forEach($scope.list, function (detail, key) {
                        if (detail['__children__'] == undefined) {
                            if (detail.pattern != null && detail.pattern.length) {
                                $scope.galleries[detail.id] = [];
                                addGalleryItem(detail, data.order != null);
                            }
                        } else {
                            angular.forEach(detail['__children__'], function (detail, key) {
                                if (detail.pattern != null && detail.pattern.length) {
                                    $scope.galleries[detail.id] = [];
                                    addGalleryItem(detail, data.order != null);
                                }
                            });
                        }
                    });
                    if (data.order != null && $scope.galleries.main.length) {
                        $scope.actions = [$scope.actionVariants.openGallery, $scope.actionVariants.default[0], $scope.actionVariants.default[1]];
                    }
                }
            }, function errorCallback(response) {
                console.log(response.statusText);
            });
        };

        $scope.refreshData();
        $timeout(function(){
            $scope.showGallery = true;
        }, 500);
    }
})();