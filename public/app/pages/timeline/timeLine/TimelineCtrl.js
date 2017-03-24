(function(){
  'use strict';
  angular.module('BlurAdmin.pages.timeline')
      .controller('TimelineCtrl', TimelineCtrl);

  /** @ngInject */
  function TimelineCtrl($scope, $rootScope, $http, $filter){
    var scopeRules = INSPECTOR_ROLE;
    if ($rootScope.$getPermissions() < scopeRules){
      $rootScope.backState();
    } else {
      $scope.$watch(function(){
        return $rootScope.$getPermissions();
      }, function(newValue, oldValue){
        if (newValue != oldValue && newValue < scopeRules){
          $rootScope.backState();
        }
      });

      $scope.list = [];
      $scope.loading = false;
      $scope.allData = false;
      $scope.entities = {
        users:   [],
        clients: [],
        orders:  [],
        details: []
      };
      $scope.filter = {
        user:   null,
        client: null,
        order:  null,
        detail: null,
        date:   {
          start: null,
          end:   null
        },
        tree:   0
      };

      $scope.actions = [
        {
          text:      "Обновить",
          class:     "btn-info",
          iconClass: "fa fa-refresh",
          action:    'clearRefreshData'
        }
      ];

      $scope.selected = {
        user:      null,
        client:    null,
        order:     null,
        detail:    null,
        dateStart: {
          date:     null,
          options:  {startingDay: 1, maxDate: new Date(Date.now())},
          check:    function(clear){
            if (!isNull(clear) && clear == true){
              $scope.selected.dateStart.date = null;
            } else if (clear == 'now' &&
                       (isNull($scope.selected.dateEnd.date) || $scope.selected.dateEnd.date < Date.now())){
              $scope.selected.dateStart.date = new Date(Date.now());
            }
            $scope.selected.dateEnd.options.minDate = $scope.selected.dateStart.date;
          },
          disabled: function(){
            return isNull($scope.selected.dateEnd.date) ? false : $scope.selected.dateEnd.date < getCropDate(Date.now());
          }
        },
        dateEnd:   {
          date:    null,
          options: {startingDay: 1, maxDate: new Date(Date.now())},
          check:   function(clear){
            if (!isNull(clear) && clear == true){
              $scope.selected.dateEnd.date = null;
            } else if (clear == 'now'){
              $scope.selected.dateEnd.date = new Date(Date.now());
            }
            if (isNull($scope.selected.dateEnd.date)){
              $scope.selected.dateStart.options.maxDate = new Date(Date.now());
            } else {
              $scope.selected.dateStart.options.maxDate = $scope.selected.dateEnd.date;
            }
          }
        },
        filterBy:  'client',
        tree:      false,
        change:    {
          client: function(){
            if (!isNull($scope.selected.client)){
              if (!isNull($scope.selected.order) && $scope.selected.order.clientId != $scope.selected.client.id){
                $scope.selected.order = null;
                $scope.selected.change.order();
              }
              $scope.selected.filterBy = 'client';
            }
          },
          order:  function(){
            if (!isNull($scope.selected.order)){
              if (!isNull($scope.selected.detail) && $scope.selected.detail.orderId != $scope.selected.order.id){
                $scope.selected.detail = null;
              }
              $scope.selected.filterBy = 'order';
            }
          },
          detail: function(){
            if (!isNull($scope.selected.detail)){
              $scope.selected.filterBy = 'detail';
            }
          },
          filter: function(){
            var edited = false;
            if ($scope.selected.user != $scope.filter.user){
              $scope.filter.user = $scope.selected.user;
              edited = true;
            }
            var client = isNull($scope.selected.client) ? null : $scope.selected.client.id;
            if ($scope.filter.client != client){
              $scope.filter.client = $scope.selected.client.id;
              edited = true;
            }
            var order = isNull($scope.selected.order) ? null : $scope.selected.order.id;
            if ($scope.filter.order != order){
              $scope.filter.order = order;
              edited = true;
            }
            var detail = isNull($scope.selected.detail) ? null : $scope.selected.detail.id;
            if ($scope.filter.detail != detail){
              $scope.filter.detail = detail;
              edited = true;
            }
            if ($scope.filter.date.start != $scope.selected.dateStart.date){
              $scope.filter.date.start = $scope.selected.dateStart.date;
              edited = true;
            }
            if ($scope.filter.date.end != $scope.selected.dateEnd.date){
              $scope.filter.date.end = $scope.selected.dateEnd.date;
              edited = true;
            }
            var tree = $scope.selected.tree == true ? 1 : 0;
            if ($scope.filter.tree != tree){
              $scope.filter.tree = tree;
              edited = true;
            }
            if (edited){
              $scope.clearRefreshData();
            }
          }
        }
      };

      $scope.clearRefreshData = function(){
        $scope.list = [];
        $scope.allData = false;
        $scope.refreshData();
      };
      $scope.refreshData = function(){
        $scope.loading = true;
        var filter = new FormData();
        if (!isNull($scope.filter.user)){
          filter.append('user', $scope.filter.user);
        }
        switch ($scope.selected.filterBy) {
          case 'client':
            if (!isNull($scope.filter.client)){
              filter.append('client', $scope.filter.client);
            }
            break;
          case 'order':
            if (!isNull($scope.filter.order)){
              filter.append('order', $scope.filter.order);
            }
            break;
          case 'detail':
            if (!isNull($scope.filter.detail)){
              filter.append('detail', $scope.filter.detail);
            }
            break;
        }
        if (!isNull($scope.filter.date.start) || !isNull($scope.filter.date.end)){
          if (!isNull($scope.filter.date.start)){
            filter.append('date[from]', $filter('date')($scope.filter.date.start, "dd.MM.yyyy"));
          }
          if (!isNull($scope.filter.date.end)){
            filter.append('date[to]', $filter('date')($scope.filter.date.end, "dd.MM.yyyy"));
          }
        }
        if (!isNull($scope.filter.tree)){
          filter.append('tree', $scope.filter.tree);
        }
        $http.post('api/events/' + $scope.list.length, filter, {
          transformRequest: angular.identity,
          headers:          {'Content-Type': undefined}
        }).then(function successCallback(response){
          var data = response.data;
          if (data.error || data.status){ $rootScope.showMessage(data); }
          if (isNull(data.error) || !data.error){
            data = data.data;
            for (var i = 0; i < data.length; i++){
              data[i].date = Date.parse(data[i].date);
              $scope.list.push(data[i]);
            }
            if (data.length < response.data.count){
              $scope.allData = true;
            }
          }
          $scope.loading = false;
        }, function errorCallback(response){
          console.log(response.statusText);
        });
      };

      $scope.endPage = function(inView){
        if (inView && !$scope.allData && !$scope.loading){
          $scope.refreshData();
        }
      };

      $http.post('api/users/get-name-list').then(function successCallback(response){
        var data = response.data;
        if (data.error || data.status){ $rootScope.showMessage(data); }
        if (isNull(data.error) || !data.error){
          $scope.entities.users = data.data;
        }
      }, function errorCallback(response){
        console.log(response.statusText);
      });
      $http.post('api/clients/only-names').then(function successCallback(response){
        var data = response.data;
        if (data.error || data.status){ $rootScope.showMessage(data); }
        if (isNull(data.error) || !data.error){
          $scope.entities.clients = data;
        }
      }, function errorCallback(response){
        console.log(response.statusText);
      });
      $http.post('api/orders/only-names').then(function successCallback(response){
        var data = response.data;
        if (data.error || data.status){ $rootScope.showMessage(data); }
        if (isNull(data.error) || !data.error){
          data.data.unshift({
            id:       -1,
            clientId: null,
            code:     "Архив"
          });
          $scope.entities.orders = data.data;
        }
      }, function errorCallback(response){
        console.log(response.statusText);
      });
      $http.post('/api/nomenclature/only-names-archive').then(function successCallback(response){
        var data = response.data;
        if (data.error || data.status){ $rootScope.showMessage(data); }
        if (isNull(data.error) || !data.error){
          $scope.entities.details = data.data;
        }
      }, function errorCallback(response){
        console.log(response.statusText);
      });
    }
  }
})();