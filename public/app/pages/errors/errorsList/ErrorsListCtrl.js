(function(){
  'use strict';
  angular.module('BlurAdmin.pages.errors')
      .controller('ErrorsListCtrl', ErrorsListCtrl);

  /** @ngInject */
  function ErrorsListCtrl($scope, $rootScope, $http, $uibModal, $filter, $state){
    $scope.scopeName = "ErrorsList";
    if (!$rootScope.$getPermissions()){
      window.history.back(-1);
    }

    $scope.$watch(function(){
      return $rootScope.$getPermissions();
    }, function(newValue, oldValue){
      if (!newValue && (newValue != oldValue)){
        if ($rootScope.previousState != null){
          $state.go($rootScope.previousState.state, $rootScope.previousState.params, {location: 'replace'});
        } else {
          window.history.back(-1);
        }
      }
    });

    $scope.errors = [];
    $scope.loading = true;
    $scope.current = null;
    $scope.search = '';
    $scope.isRead = true;

    $scope.actions = [
      {
        text:      "Обновить",
        class:     "btn-info",
        iconClass: "fa fa-refresh",
        action:    'refreshData'
      }
    ];

    $scope.filtred = [];
    $scope.$watch("search + isRead", function(){
      var filter = {title: $scope.search};
      if ($scope.isRead){
        delete filter.read;
      } else {
        filter.read = false;
      }
      $scope.filtred = $filter('filter')($scope.errors, filter);
    });

    $scope.$watch('filtred', function(newValue){
      if (Array.isArray(newValue)){
        $scope.select.check(newValue);
      }
    });

    $scope.showError = function(id){
      for (var i = 0; i < $scope.errors.length; i++){
        if ($scope.errors[i].id == id){
          $scope.current = i;
          $http.post('/api/errors/set-read/' + id, null).then(function successCallback(response){
            var data = response.data;
            if (data.error || data.status){ $rootScope.showMessage(data); }
            if (isNull(data.error) || !data.error){
              $scope.errors[i].read = true;
            }
          }, function errorCallback(response){
            console.log(response.statusText);
          });
          break;
        }
      }
      $scope.modal.open();
    };

    var modalInstance = null;
    $scope.modal = {
      open: function(){
        modalInstance = $uibModal.open({
          animation:   true,
          templateUrl: 'app/pages/errors/modal/error.html',
          size:        'exlg',
          backdrop:    'static',
          scope:       $scope
        });
        modalInstance.closed.then(function(){
          modalInstance = null;
          $scope.current = null;
        });
      }
    };

    $scope.select = {
      checked:    0,
      checkedAll: false,
      check:      function(list){
        if (list.length > 0){
          var allChecked = true,
              count = 0;
          angular.forEach(list, function(err, key){
            if (isNull(err.checked) || !err.checked){
              allChecked = false;
            } else if (err.checked){
              count++;
            }
          });
          $scope.select.checkedAll = allChecked;
          $scope.select.checked = count;
        } else {
          $scope.select.checkedAll = false;
          $scope.select.checked = 0;
        }
      },
      checkAll:   function(list){
        angular.forEach(list, function(err, key){
          err.checked = $scope.select.checkedAll;
        });
        $scope.select.checked = list.length;
      }
    };

    $scope.updateError = function(task, id){
      var arrIds = [], i;
      if (id == undefined){
        if (task == "drop"){
          var drop = [];
          for (i = 0; i < $scope.errors.length; i++){
            if ((!isNull($scope.errors[i].checked) && $scope.errors[i].checked)){
              arrIds.push($scope.errors[i].id);
              drop.push(i);
            }
          }
        } else {
          var read = (task == "read");
          for (i = 0; i < $scope.errors.length; i++){
            if ((!isNull($scope.errors[i].checked) && $scope.errors[i].checked) && $scope.errors[i].read != read){
              arrIds.push($scope.errors[i].id);
            }
          }
        }
      } else {
        arrIds = [$scope.errors[id].id];
      }

      if (arrIds.length > 0){
        var send = new FormData();
        for (i = 0; i < arrIds.length; i++){
          send.append('ids[]', arrIds[i]);
        }

        switch (task) {
          case "unread":
            task = "set-unread";
            break;
          case "drop":
            task = "del-error";
            break;
          default:
            task = "set-read";
            break;
        }
        $http.post("/api/errors/" + task, send, {
          transformRequest: angular.identity,
          headers:          {'Content-Type': undefined}
        }).then(function successCallback(response){
          var data = response.data;
          if (data.error || data.status){ $rootScope.showMessage(data); }
          if (isNull(data.error) || !data.error){
            var setRead = null;
            switch (task) {
              case "del-error":
                if (id == undefined){
                  for (i = drop.length - 1; i >= 0; i--){
                    $scope.errors.splice(drop[i], 1);
                  }
                } else {
                  $scope.errors.splice(id, 1);
                }
                if (modalInstance != null){
                  modalInstance.dismiss('close');
                }
                break;
              case "set-unread":
                setRead = false;
              default:
                if (setRead == null){
                  setRead = true;
                }
                if (id == undefined){
                  for (i = 0; i < $scope.errors.length; i++){
                    if ((!isNull($scope.errors[i].checked) && $scope.errors[i].checked) &&
                        $scope.errors[i].read != setRead){
                      $scope.errors[i].read = setRead;
                    }
                  }
                } else {
                  $scope.errors[id].read = setRead;
                }
                break;
            }
          }
          for (i = 0; i < $scope.errors.length; i++){
            $scope.errors[i].checked = false;
          }
          $scope.select.checked = 0;
          $scope.select.checkedAll = false;
        }, function errorCallback(response){
          console.log(response.statusText);
        });
      }
    };

    $scope.refreshData = function(){
      $scope.loading = true;

      $http.post('/api/errors', null).then(function successCallback(response){
        var data = response.data;
        if (data.error || data.status){ $rootScope.showMessage(data); }
        if (isNull(data.error) || !data.error){
          $scope.errors = $scope.filtred = data.data;
          angular.forEach($scope.errors, function(error, key){
            error.date = new Date(error.date);
          });
        }
        $scope.loading = false;
      }, function errorCallback(response){
        console.log(response.statusText);
      });
    };

    $scope.refreshData();

    $scope.test = function(){
      console.log(document.referrer);
    }
  }
})();