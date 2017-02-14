(function () {
  'use strict';
  angular.module('BlurAdmin.pages.orders')
      .controller('OrdersListCtrl', OrdersListCtrl);

  /** @ngInject */
  function OrdersListCtrl($scope, $stateParams, $http) {
    $scope.showClient = ($stateParams.id == null);
    $scope.clientName = null;
    $scope.defList = [];
    $scope.list = [];
    $scope.loading = true;

    $scope.filter = {1:true, 2:true, 3:false};
    $scope.propertyName = 'dateCreation';
    $scope.reverse = true;

    $scope.actions =  [
      {
        text: "Обновить",
        class: "btn-info",
        iconClass: "fa fa-refresh",
        action: 'refreshData'
      }
    ];

    function getCropDate(date) {
      if (date !== null && date instanceof Date) {
        return new Date(date.getFullYear(), date.getMonth(), date.getDate());
      } else if (date !== null && typeof(date) == 'number') {
        date = new Date(date);
        return new Date(date.getFullYear(), date.getMonth(), date.getDate());
      } else {
        return null
      }
    }

    $scope.sortBy = function(propertyName) {
      if (propertyName.indexOf('date') != -1) {
        $scope.reverse = ($scope.propertyName === propertyName) ? !$scope.reverse : true;
      }
      else {
        $scope.reverse = ($scope.propertyName === propertyName) ? !$scope.reverse : false;
      }
      $scope.propertyName = propertyName;
    };

    function noFilter(filterObj) {
      return Object.
      keys(filterObj).
      every(function (key) { return !filterObj[key]; });
    }

    $scope.filterBy = function(item) {
      return $scope.filter[item.statusCode] || noFilter($scope.filter);
    };

    $scope.refreshData = function () {
      $scope.loading = true;
      if ($stateParams.id == null) {
        $scope.clientName = null;
      }

      var $url = ($stateParams.id == null) ? "/api/orders" : "/api/orders/get-by-client/" + $stateParams.id;
      $http.post($url).then(function successCallback(response) {
        var data = response.data;
          if (data.error) {
            console.log(data);
          } else {
            $scope.list = data.data;
            angular.forEach($scope.list, function (order, key) {
                $scope.list[key]['dateCreation'] = (order.dateCreation) ? new Date(order.dateCreation) : null;
                $scope.list[key]['dateStart'] = (order.dateStart) ? new Date(order.dateStart) : null;
                $scope.list[key]['dateEnd'] = (order.dateEnd) ? new Date(order.dateEnd) : null;
                $scope.list[key]['dateDeadline'] = (order.dateDeadline) ? new Date(order.dateDeadline) : null;
            });
            $scope.original = angular.copy($scope.list);
            $scope.loading = false;
            if (data.clientName != null) {
              $scope.clientName = data.clientName;
            }
          }
      }, function errorCallback(response) {
        console.log(response.statusText);
      });
    };

    $scope.refreshData();
    $scope.calendar = {
        options: {startingDay: 1},
        dateCreation: {
            change: function () {
                $scope.calendar.dateStart.options.minDate = $scope.calendar.dateCreation.date;
                if ($scope.calendar.dateEnd.options.minDate == null) {
                    $scope.calendar.dateEnd.options.minDate = $scope.calendar.dateCreation.date;
                }
                if ($scope.calendar.dateDeadline.options.minDate == null) {
                    $scope.calendar.dateDeadline.options.minDate = $scope.calendar.dateCreation.date;
                }
                if ($scope.calendar.dateStart.date !== null
                    && getCropDate($scope.calendar.dateCreation.date) > getCropDate($scope.calendar.dateStart.date)) {
                    $scope.calendar.dateStart.date = null;
                }
            }
        },
        dateStart: {
            change: function () {
                $scope.calendar.dateEnd.options.minDate = $scope.calendar.dateStart.date;
                if ($scope.calendar.dateDeadline.options.minDate == null) {
                    $scope.calendar.dateDeadline.options.minDate = $scope.calendar.dateStart.date;
                }
                if ($scope.calendar.dateEnd.date !== null
                    && getCropDate($scope.calendar.dateStart.date) > getCropDate($scope.calendar.dateEnd.date)) {
                    $scope.calendar.dateEnd.date = null;
                }
            }
        },
        dateEnd: {
            change: function () {
                $scope.calendar.dateDeadline.options.minDate = $scope.calendar.dateEnd.date;
                if ($scope.calendar.dateDeadline.date !== null
                    && getCropDate($scope.calendar.dateEnd.date) > getCropDate($scope.calendar.dateDeadline.date)) {
                    $scope.calendar.dateDeadline.date = null;
                }
            }
        },
        today: function (model) {
            if (model === $scope.calendar.dateCreation) {
                $scope.calendar.dateCreation.date = getCropDate(Date.now());
                if ($scope.calendar.dateStart.date !== null
                    && getCropDate($scope.calendar.dateCreation.date) > getCropDate($scope.calendar.dateStart.date)) {
                    $scope.calendar.dateStart.date = getCropDate(Date.now());
                }
                if ($scope.calendar.dateEnd.date !== null
                    && getCropDate($scope.calendar.dateCreation.date) > getCropDate($scope.calendar.dateEnd.date)) {
                    $scope.calendar.dateEnd.date = getCropDate(Date.now());
                }
            } else if (model === $scope.calendar.dateStart) {
                if ($scope.calendar.dateCreation.date !== null) {
                    if (getCropDate($scope.calendar.dateCreation.date) <= getCropDate(Date.now())) {
                        $scope.calendar.dateStart.date = getCropDate(Date.now());
                    } else {
                        $scope.calendar.dateStart.date = new Date($scope.calendar.dateCreation.date.valueOf());
                        $scope.calendar.dateStart.date.setDate($scope.calendar.dateCreation.date.getDate() + 1);
                    }

                    if ($scope.calendar.dateEnd.date !== null
                        && getCropDate($scope.calendar.dateStart.date) > getCropDate($scope.calendar.dateEnd.date)) {
                        $scope.calendar.dateEnd.date = getCropDate(Date.now());
                    }
                }
            } else if (model === $scope.calendar.dateEnd) {
                if ($scope.calendar.dateStart.date !== null) {
                    if (getCropDate($scope.calendar.dateStart.date) <= getCropDate(Date.now())) {
                        $scope.calendar.dateEnd.date = getCropDate(Date.now());
                    } else {
                        $scope.calendar.dateEnd.date = new Date($scope.calendar.dateStart.date.valueOf());
                        $scope.calendar.dateEnd.date.setDate($scope.calendar.dateStart.date.getDate() + 1);
                    }
                }
            }
        },
        clear: function (model) {
            if (model === $scope.calendar.dateCreation) {
                $scope.calendar.dateCreation.date = null;
                $scope.calendar.dateStart.date = null;
                $scope.calendar.dateEnd.date = null;
            } else if (model === $scope.calendar.dateStart) {
                $scope.calendar.dateStart.date = null;
                $scope.calendar.dateEnd.date = null;
            } else {
                model.date = null;
            }
        }
  };
    $scope.editItem = function(item){
        item.edit = true;
    };
    $scope.saveItem = function(item){
        item.edit = false;
        for (var i = 0; i < $scope.list.length; i++) {
            if ($scope.list[i].id == item.id) {
                $scope.original[i] = angular.copy(item);
                break;
            }
        }
    };
    $scope.abortItem = function(item){
        item.edit = false;
        for (var i = 0; i < $scope.original.length; i++) {
            if ($scope.original[i].id == item.id) {
                item = angular.copy($scope.original[i]);
                break;
            }
        }
    };
  }
})();