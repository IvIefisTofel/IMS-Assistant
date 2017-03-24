(function(){
  'use strict';
  angular.module('BlurAdmin.theme.components')
      .controller('PageTopCtrl', PageTopCtrl);

  /** @ngInject */
  function PageTopCtrl($scope, $rootScope, $http, Idle, $uibModal){
    $scope.user = $rootScope.$user();
    $scope.USER_ROLE = USER_ROLE;
    $scope.currentUserId = $scope.user.id;
    $scope.editable = {};
    $scope.showErrors = false;

    var userModalInstance = null;
    $scope.userSaved = function(response){
      var user = response.data;
      if (user.id == $scope.user.id) {
        $scope.user = user;
        var scope = angular.element($('div[ui-view]').children()).scope();
        if (!isNull(scope.scopeName) && scope.scopeName == "UserList"){
          scope.saved(response, true);
        }
      }
    };
    $scope.userModal = $rootScope.userEdit(userModalInstance, $scope, $scope.userSaved);
    $scope.userEdit = function(){
      $scope.editable = $scope.user;
      $scope.userModal.open();
    };

    $scope.reLogin = function(){
      modalInstance.dismiss('close');
      var form = new FormData();
      form.append('assistant_name', $scope.modalActions.user);
      form.append('assistant_password', $scope.modalActions.password ? $scope.modalActions.password : '');

      $http.post('/login', form, {
        transformRequest: angular.identity,
        headers:          {'Content-Type': undefined}
      }).then(function successCallback(response){
        if (response.data.auth){
          if (response.data.permissions > USER_ROLE){
            Idle.watch();
          } else {
            Idle.unwatch();
          }
          $scope.user = $rootScope.$user();
        } else {
          console.log(response.data);
        }
      }, function errorCallback(response){
        console.log(response);
      });
    };

    $scope.userList = [];
    $http.post('/api/users/get-name-list', null).then(function successCallback(response){
      var data = response.data;
      if (data.error){
        console.log(data);
      } else {
        $scope.userList = data.data;
        $scope.modalActions.user = $scope.user.name;
      }
    }, function errorCallback(response){
      console.log(response.statusText);
    });

    var modalInstance = null;
    $scope.modalActions = {
      user:     null,
      password: null,
      reLogin:  function(){
        if (modalInstance != null){
          modalInstance.dismiss('close');
        }
        $scope.modalActions.user = $scope.user.name;
        $scope.modalActions.password = null;
        modalInstance = $uibModal.open({
          animation:   true,
          templateUrl: 'app/theme/components/pageTop/modal/reLogin.html',
          size:        'md',
          backdrop:    'static',
          scope:       $scope
        });
        modalInstance.closed.then(function(){
          modalInstance = null;
        })
      }
    };
  }
})();