/**
 * @author v.lugovksy
 * created on 16.12.2015
 */
(function () {
  'use strict';

  angular.module('BlurAdmin.pages.nomenclature')
      .controller('DetailCtrl', DetailCtrl);

  /** @ngInject */
  function DetailCtrl($scope, $stateParams, $http, $window, $timeout, $interval) {
    function isEmpty(obj) {
      if (obj == null) return true;
      if (obj.length > 0)    return false;
      if (obj.length === 0)  return true;
      if (typeof obj !== "object") return true;
      for (var key in obj) {
        if (Object.prototype.hasOwnProperty.call(obj, key)) return false;
      }

      return true;
    }
    function getCropDate(date){
      if (date !== null && date instanceof Date) {
        return new Date(date.getFullYear(), date.getMonth(), date.getDate());
      } else if (date !== null && typeof(date) == 'number') {
        date = new Date(date);
        return new Date(date.getFullYear(), date.getMonth(), date.getDate());
      } else {
        return null
      }
    }

    $scope.actionVariants =  {
      view: [
        {
          text: "Редактировать",
          class: "btn-primary",
          iconClass: "fa fa-pencil",
          action: 'switchEdit'
        },
        {
          text: "Обновить",
          class: "btn-info",
          iconClass: "fa fa-refresh",
          action: 'refreshData'
        }
      ],
      edit: [
        {
          text: "Сохранить",
          class: "btn-success",
          iconClass: "fa fa-floppy-o",
          action: 'saveData'
        },
        {
          text: "Отменить",
          class: "btn-danger",
          iconClass: "fa fa-ban",
          action: 'switchEdit'
        },
        {
          text: "Обновить",
          class: "btn-info",
          iconClass: "fa fa-refresh",
          action: 'refreshData'
        }
      ]
    };
    $scope.actions = $scope.actionVariants.view;

    $scope.edit = false;
    $scope.preview = {
      detail: null,
      clients: null,
      orders: null
    };

    $scope.detail = {};
    $scope.orders  = [];
    $scope.loading = true;

    $scope.selected = {
      client: null,
      order: null
    };
    $scope.ordersObj = {
      client: {
        change: function() {
          if (!isEmpty($scope.selected.client)) {
            $scope.selected.order = undefined;
          }
        }
      },
      order: {
        change: function() {
          if (!isEmpty($scope.selected.order)) {
            $scope.selected.client = {key: $scope.clients[$scope.selected.order.clientId].id, value: $scope.clients[$scope.selected.order.clientId]};
          }
        }
      },
      clear: function(){
        $scope.selected.client = undefined;
        $scope.selected.order = undefined;
      }
    };

    $scope.calendar = {
      dateCreation: {
        options: {
          startingDay: 1
        },
        date: Date.now(),
        change: function(){
          $scope.calendar.dateEnd.options.minDate = $scope.calendar.dateCreation.date;
          if ($scope.calendar.dateEnd.date !== null
              && getCropDate($scope.calendar.dateCreation.date) > getCropDate($scope.calendar.dateEnd.date)) {
            $scope.calendar.dateEnd.date = null;
          }
        },
        open: function() {
          $scope.calendar.dateCreation.opened = true;
        },
        opened: false,
        tooltips: {
          today: false,
          clear: false,
          calendar: false
        }
      },
      dateEnd: {
        options: {
          startingDay: 1,
          minDate: null
        },
        date: null,
        open: function() {
          $scope.calendar.dateEnd.opened = true;
        },
        opened: false,
        tooltips: {
          today: false,
          clear: false,
          calendar: false
        }
      },
      today: function(model) {
        if (model === $scope.calendar.dateCreation) {
          $scope.calendar.dateCreation.date = getCropDate(Date.now());
          if ($scope.calendar.dateEnd.date !== null
              && getCropDate($scope.calendar.dateCreation.date) > getCropDate($scope.calendar.dateEnd.date)) {
            $scope.calendar.dateEnd.date = getCropDate(Date.now());
          }
        } else if (model === $scope.calendar.dateEnd) {
          if ($scope.calendar.dateCreation.date !== null) {
            if (getCropDate($scope.calendar.dateCreation.date) <= getCropDate(Date.now())) {
              $scope.calendar.dateEnd.date = getCropDate(Date.now());
            } else {
              $scope.calendar.dateEnd.date = new Date($scope.calendar.dateCreation.date.valueOf());
              $scope.calendar.dateEnd.date.setDate($scope.calendar.dateCreation.date.getDate() + 1);
            }
          }
        }
      },
      clear: function (model) {
        if (model === $scope.calendar.dateCreation) {
          $scope.calendar.dateCreation.date = null;
          $scope.calendar.dateEnd.date = null;
        } else {
          model.date = null;2
        }
      }
    };

    $scope.filterBy = function(item) {
      if (!isEmpty($scope.selected.client)) {
        return item.clientId == $scope.selected.client.id;
      } else {
        return true;
      }
    };

    $scope.switchEdit = function(){
      if ($scope.edit) {
        $scope.edit = false;
        $scope.actions = $scope.actionVariants.view;
        $scope.detail = angular.copy($scope.preview.detail);
        $scope.clients = $scope.orders = [];
        angular.forEach($scope.preview.clients, function(client, key) {
          angular.forEach(client.orders, function(order, key) {
            if ($scope.detail.orderId == order.id) {
              $scope.selected.client = client;
              $scope.selected.order = order;
            }
            $scope.orders = $scope.orders.concat(order);
          });
          $scope.clients = $scope.clients.concat(client);
        });
        $scope.calendar.dateCreation.date =
            $scope.calendar.dateEnd.options.minDate = new Date($scope.preview.detail.dateCreation);
        $scope.calendar.dateEnd.date = new Date($scope.preview.detail.dateEnd);
      } else {
        $scope.edit = true;
        $scope.actions = $scope.actionVariants.edit;
      }
    };

    $scope.saveData = function(){
      console.log('Saving data!');
    };

    $scope.refreshData = function () {
      if ($stateParams.id == null) {
        $window.history.back();
      }
      $scope.loading = true;
      if ($scope.edit) {
        $scope.edit = false;
        $scope.actions = $scope.actionVariants.view;
      }

      var $url = "/api/nomenclature/get-with-parents/" + $stateParams.id;
      $http.post($url).then(function successCallback(response) {
        var data = response.data;
        if (data.error) {
          console.log(data);
        } else {
          $scope.detail = data.data[0];
          $scope.clients = data.clients;
          $scope.calendar.dateCreation.date =
              $scope.calendar.dateEnd.options.minDate = new Date($scope.detail.dateCreation);
          $scope.calendar.dateEnd.date = new Date($scope.detail.dateEnd);
          angular.forEach($scope.clients, function(client, key) {
            angular.forEach(client.orders, function(order, key) {
              if ($scope.detail.orderId == order.id) {
                $scope.selected.client = client;
                $scope.selected.order = order;
              }
              $scope.orders = $scope.orders.concat(order);
            });
          });
          $scope.preview = {
            detail: jQuery.extend(true, {}, $scope.detail),
            clients: jQuery.extend(true, {}, $scope.clients),
            orders: jQuery.extend(true, {}, $scope.orders)
          };
          $scope.loading = false;
        }
      }, function errorCallback(response) {
        console.log(response.statusText);
      });
    };

    $scope.refreshData();

    $scope.test = function(){
      console.log($scope.detail.name);
      console.log($scope.preview.detail.name);
      // $scope.selected.client = JSON.parse(JSON.stringify($scope.selected.client));
      // console.log($scope.selected.client.$$hashKey);
    };
  }
})();