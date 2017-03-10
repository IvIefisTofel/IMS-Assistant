(function () {
  'use strict';
  angular.module('BlurAdmin.theme.components')
      .controller('MsgCenterCtrl', MsgCenterCtrl);

  /** @ngInject */
  function MsgCenterCtrl($scope, $http, $interval, $uibModal) {
    $scope.hotOrders = [];
    $scope.errors = [];
    $scope.current = null;

    $scope.updateError = function(task, id){
      var send = new FormData();
      send.append('ids[]', id);

      switch (task) {
        case "unread": task = "set-unread"; break;
        case "drop": task = "del-error"; break;
        default: task = "set-read"; break;
      }
      $http.post("/api/errors/" + task, send, {
        transformRequest: angular.identity,
        headers: {'Content-Type': undefined}
      }).then(function successCallback(response) {
        var data = response.data;
        if (data.error) {
          console.log(data);
        } else {
          var setRead = null, i,
              scope = angular.element($('div[ui-view]').children()).scope();
          if (isNull(scope.scopeName) || scope.scopeName != "ErrorsList") {
            scope = null;
          }
          switch (task) {
            case "del-error":
              $scope.errors.splice(id, 1);
              if (scope !== null) {
                for (i = 0; i < scope.errors.length; i++) {
                  if (scope.errors[i].id == $scope.errors[id].id) {
                    $scope.errors.splice(i, 1);
                    break;
                  }
                }
              }
              if (modalInstance != null) {
                modalInstance.dismiss('close');
              }
              break;
            case "set-unread": setRead = false;
            default:
              if (setRead == null) {
                setRead = true;
              }
              $scope.errors[id].read = setRead;
              if (scope !== null) {
                for (i = 0; i < scope.errors.length; i++) {
                  if (scope.errors[i].id == $scope.errors[id].id) {
                    scope.errors[i].read = setRead;
                    break;
                  }
                }
              }
              break;
          }
        }
      }, function errorCallback(response) {
        console.log(response.statusText);
      });
    };

    $scope.showError = function(id) {
      for (var i = 0; i < $scope.errors.length; i++) {
        if ($scope.errors[i].id == id) {
          $scope.current = i;
          $http.post('/api/errors/set-read/' + id, null).then(function successCallback(response) {
            var data = response.data;
            if (data.error) {
              console.log(data);
            } else {
              $scope.errors[i].read = true;
              var scope = angular.element($('div[ui-view]').children()).scope();
              if (!isNull(scope.scopeName) && scope.scopeName == "ErrorsList") {
                for (var j = 0; j < scope.errors.length; j++) {
                  if (scope.errors[j].id == id) {
                    scope.errors[j].read = true;
                    break;
                  }
                }
              }
            }
          }, function errorCallback(response) {
            console.log(response.statusText);
          });
          break;
        }
      }
      $scope.modal.open();
    };

    var modalInstance = null;
    $scope.modal = {
      open: function () {
        modalInstance = $uibModal.open({
          animation: true,
          templateUrl: 'app/pages/errors/modal/error.html',
          size: 'exlg',
          backdrop: 'static',
          scope: $scope
        });
        modalInstance.closed.then(function(){
          modalInstance = null;
          $scope.current = null;
        });
      }
    };

    $scope.update = function () {
      $http.post('/api/services/notifications', null).then(function successCallback(response) {
        var data = response.data;
        if (data.error) {
          console.log(data);
        } else {
          $scope.errors = data.errors;
          $scope.hotOrders = data.hotOrders;
          angular.forEach($scope.errors, function(error, key){
            error.date = new Date(error.date);
          });
          angular.forEach($scope.hotOrders, function(item, key){
            item.dateDeadline = !isNull(item.dateDeadline) ? new Date(item.dateDeadline) : null;
            item.expired = (item.dateDeadline < Date.now());
          });
        }
      }, function errorCallback(response) {
        console.log(response.statusText);
      });
    };

    $scope.update();
    $interval(function () {
      $scope.update();
    }, 60000 * 5);
  }
})();