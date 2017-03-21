(function(){
  'use strict';
  angular.module('BlurAdmin.pages.users')
      .controller('UsersListCtrl', UsersListCtrl);

  /** @ngInject */
  function UsersListCtrl($scope, $rootScope, $state, $http, $filter, $uibModal){
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

    $scope.usersOriginal = [];
    $scope.currentUserId = $rootScope.user.id;
    $scope.users = [];
    $scope.search = {};
    $scope.active = true;
    $scope.propertyName = 'fullName';
    $scope.reverse = false;
    $scope.editable = {};
    $scope.showErrors = false;

    function filter(){
      $scope.users = $filter('orderBy')(
          $filter('filter')($scope.usersOriginal, {$: $scope.search.$, active: $scope.active}), $scope.propertyName,
          $scope.reverse);
    }

    $scope.$watch("search.$ + propertyName + reverse + active", function(){
      filter();
    });

    $scope.$watch(function(){ return $rootScope.user }, function(newVal){
      if (isNull(newVal)){
        $scope.currentUserId = null;
      } else {
        $scope.currentUserId = newVal.id;
      }
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
      $scope.modal.open();
    };

    $scope.add = function(){
      $scope.editable = {
        grAvatar:         'http://ru.gravatar.com/avatar/?d=mm',
        registrationDate: Date.now(),
        active:           true
      };
      $scope.modal.open();
    };

    var modalInstance = null;
    $scope.modal = {
      open: function(){
        modalInstance = $uibModal.open({
          animation:   true,
          templateUrl: 'app/pages/users/modal/editUser.html',
          size:        'md',
          backdrop:    'static',
          scope:       $scope
        });
        modalInstance.closed.then(function(){
          modalInstance = null;
          $scope.editable = {};
          $scope.showErrors = false;
        });
      }
    };

    $scope.refreshData = function(){
      $scope.loading = true;

      $http.post('api/users/').then(function successCallback(response){
        var data = response.data;
        if (data.error){
          console.log(data);
        } else {
          $scope.usersOriginal = data.data;
          angular.forEach($scope.usersOriginal, function(user, key){
            user.registrationDate = (user.registrationDate) ? Date.parse(user.registrationDate) : null;
          });
          filter();
          $scope.loading = false;
        }
      }, function errorCallback(response){
        console.log(response.statusText);
      });
    };

    $scope.refreshData();
  }
})();