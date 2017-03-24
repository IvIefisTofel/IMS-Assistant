(function(){
  'use strict';
  angular.module('BlurAdmin.pages.users')
      .controller('UsersListCtrl', UsersListCtrl);

  /** @ngInject */
  function UsersListCtrl($scope, $rootScope, $http, $filter){
    $scope.scopeName = "UserList";
    var scopeRules = SUPERVISOR_ROLE;
    function filter(){
      $scope.users = $filter('orderBy')(
          $filter('filter')($scope.usersOriginal, {$: $scope.search.$, active: $scope.active}), $scope.propertyName,
          $scope.reverse);
    }

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

      $scope.usersOriginal = [];
      $scope.users = [];
      $scope.search = {};
      $scope.active = true;
      $scope.propertyName = 'fullName';
      $scope.reverse = false;
      $scope.editable = {};
      $scope.showErrors = false;

      $scope.$watch("search.$ + propertyName + reverse + active", function(){
        filter();
      });

      $scope.actions = [
        {
          text:      "Добавить",
          class:     "btn-success",
          iconClass: "fa fa-user-plus",
          action:    'add'
        },
        {
          text:      "Обновить",
          class:     "btn-info",
          iconClass: "fa fa-refresh",
          action:    'refreshData'
        }
      ];

      $scope.sortBy = function(propertyName){
        if (propertyName == 'registrationDate'){
          $scope.reverse = ($scope.propertyName === propertyName) ? !$scope.reverse : true;
        } else {
          $scope.reverse = ($scope.propertyName === propertyName) ? !$scope.reverse : false;
        }
        $scope.propertyName = propertyName;
      };

      $scope.edit = function(user){
        $scope.editable = angular.copy(user);
        $scope.userModal.open();
      };

      $scope.add = function(){
        $scope.editable = {
          grAvatar:         'http://ru.gravatar.com/avatar/?d=mm',
          registrationDate: Date.now(),
          active:           true,
          roleId:           3
        };
        $scope.userModal.open();
      };

      $scope.passwordType = $scope.newPasswordType = $scope.confirmPasswordType = 'password';
      var modalInstance = null;
      $scope.saved = function(response, fromPageTop){
        var user = response.data[0];
        if (isNull($scope.editable.id) && fromPageTop != true) {
          $scope.usersOriginal.push(user);
        } else {
          $rootScope.user = user;
          for (var i = 0; i < $scope.usersOriginal.length; i++) {
            if ($scope.usersOriginal[i].id == user.id) {
              $scope.usersOriginal[i] = user;
              break;
            }
          }
        }
        filter();
      };
      $scope.userModal = $rootScope.userEdit(modalInstance, $scope, $scope.saved);

      $scope.refreshData = function(){
        $scope.loading = true;

        $http.post('api/users/').then(function successCallback(response){
          var data = response.data;
          if (data.error || data.status){ $rootScope.showMessage(data); }
          if (isNull(data.error) || !data.error){
            $scope.usersOriginal = data.data;
            angular.forEach($scope.usersOriginal, function(user, key){
              user.registrationDate = (user.registrationDate) ? Date.parse(user.registrationDate) : null;
            });
            filter();
          }
          $scope.loading = false;
        }, function errorCallback(response){
          console.log(response.statusText);
        });
      };

      $scope.refreshData();
    }
  }
})();