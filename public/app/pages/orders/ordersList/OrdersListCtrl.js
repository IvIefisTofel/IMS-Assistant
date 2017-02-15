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

    $scope.actions = [
      {
        text: "Обновить",
        class: "btn-info",
        iconClass: "fa fa-refresh",
        action: 'refreshData'
      }
    ];
    var statuses = {
      1: 'Заказ',
      2: 'В работе',
      3: 'Исполнено'
    };

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
      return Object.keys(filterObj).every(function (key) {
        return !filterObj[key];
      });
    }

    $scope.filterBy = function(item) {
      return $scope.filter[item.statusCode] || noFilter($scope.filter);
    };

    $scope.selected = {client: null};
    $scope.clients = {obj: null, arr: null};
    $scope.calendar = {
      dateCreation: {options: {startingDay: 1}},
      dateStart: {options: {startingDay: 1}},
      dateEnd: {options: {startingDay: 1}},
      dateDeadline: {options: {startingDay: 1}},
      init: function () {
        $scope.calendar.dateStart.options.minDate = $scope.listEdit.editable.dateCreation;
        $scope.calendar.dateDeadline.options.minDate = $scope.listEdit.editable.dateCreation;
        if ($scope.listEdit.editable.dateStart != null) {
          $scope.calendar.dateEnd.options.minDate = $scope.listEdit.editable.dateStart;
        } else {
          $scope.calendar.dateEnd.options.minDate = $scope.calendar.dateStart.options.minDate;
        }
      },
      checkDate: function (state, minDate) {
        if (minDate != undefined && minDate != null) {
          minDate = getCropDate(minDate);
        } else {
          minDate = null;
        }
        switch (state) {
          case 'dateCreation':
          case 1:
            if (minDate !== null) {
              if ($scope.listEdit.editable.dateStart !== null
                  && minDate > getCropDate($scope.listEdit.editable.dateStart)) {
                $scope.listEdit.editable.dateStart = null;
              }
              if ($scope.listEdit.editable.dateDeadline !== null
                  && minDate > getCropDate($scope.listEdit.editable.dateDeadline)) {
                $scope.listEdit.editable.dateDeadline = null;
              }
            }
            $scope.calendar.dateStart.options.minDate = $scope.calendar.dateDeadline.options.minDate = minDate;
          case 'dateStart':
          case 2:
            if (minDate !== null && $scope.listEdit.editable.dateEnd !== null
                && minDate > getCropDate($scope.listEdit.editable.dateEnd)) {
              $scope.listEdit.editable.dateEnd = null;
            }
            if ($scope.listEdit.editable.dateStart == null) {
              $scope.calendar.dateEnd.options.minDate = $scope.listEdit.editable.dateCreation;
            } else {
              $scope.calendar.dateEnd.options.minDate = $scope.listEdit.editable.dateStart;
            }
        }
      },
      today: function (model) {
        var now = getCropDate(Date.now());
        if (model === $scope.calendar.dateCreation) {
          $scope.listEdit.editable.dateCreation = now;
          $scope.calendar.checkDate('dateCreation', now);
        } else if (model === $scope.calendar.dateStart) {
          $scope.listEdit.editable.dateStart = now;
          $scope.calendar.checkDate('dateStart', now);
        } else if (model === $scope.calendar.dateEnd) {
          $scope.listEdit.editable.dateEnd = now;
          $scope.calendar.checkDate('dateEnd', now);
        } else if (model === $scope.calendar.dateDeadline) {
          $scope.listEdit.editable.dateDeadline = now;
        }
      },
      clear: function (model) {
        if (model === $scope.calendar.dateStart) {
          $scope.listEdit.editable.dateStart = null;
          $scope.listEdit.editable.dateEnd = null;
          $scope.calendar.checkDate('dateStart');
        } else if (model === $scope.calendar.dateEnd) {
          $scope.listEdit.editable.dateEnd = null;
          $scope.calendar.checkDate('dateEnd');
        } else if (model === $scope.calendar.dateDeadline) {
          $scope.listEdit.editable.dateDeadline = null;
        }
      }
    };
    $scope.listEdit = {editable: {}};
    var lastEditId = null;
    $scope.editItem = function (item) {
      if (lastEditId == null) {
        lastEditId = item.id;
      } else {
        $scope.listEdit[lastEditId] = false;
        lastEditId = item.id;
      }
      $scope.listEdit.editable = angular.copy(item);
      $scope.calendar.init();
      $scope.selected.client = angular.copy($scope.clients.obj[item.clientId]);
      $scope.listEdit[item.id] = true;
    };
    $scope.saveItem = function (item) {
      item.code = $scope.listEdit.editable.code;
      item.clientId = $scope.selected.client.id;
      item.clientName = $scope.selected.client.name;
      item.dateCreation = $scope.listEdit.editable.dateCreation;
      item.dateStart = $scope.listEdit.editable.dateStart;
      item.dateEnd = $scope.listEdit.editable.dateEnd;
      item.dateDeadline = $scope.listEdit.editable.dateDeadline;
      if (item.dateEnd != null) {
        item.statusCode = 3;
        item.status = statuses[item.statusCode];
      } else if (item.dateStart != null) {
        item.statusCode = 2;
        item.status = statuses[item.statusCode];
      } else {
        item.statusCode = 1;
        item.status = statuses[item.statusCode];
      }
      $scope.listEdit[item.id] = false;
    };
    $scope.abortItem = function (item) {
      $scope.listEdit[item.id] = false;
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
          $scope.listEdit = {
            editable: {}
          };
          if (data.clientName != null) {
            $scope.clientName = data.clientName;
          }
          $scope.loading = false;
        }
      }, function errorCallback(response) {
        console.log(response.statusText);
      });
      $http.post('api/clients/only-names').then(function successCallback(response) {
        var data = response.data;
        if (data.error) {
          console.log(data);
        } else {
          $scope.clients.obj = data;
          $scope.clients.arr = Object.values($scope.clients.obj);
        }
      }, function errorCallback(response) {
        console.log(response.statusText);
      });
    };

    $scope.refreshData();
  }
})();