(function () {
  'use strict';
  angular.module('BlurAdmin.pages.users')
      .controller('UsersListCtrl', UsersListCtrl);

  /** @ngInject */
  function UsersListCtrl($scope, $rootScope, $state) {
    if (!$rootScope.$getPermissions()) {
      window.history.back(-1);
    }

    $scope.$watch(function(){
      return $rootScope.$getPermissions();
    }, function(newValue, oldValue){
      if (!newValue && (newValue != oldValue)) {
        if ($rootScope.previousState != null) {
          $state.go($rootScope.previousState.state, $rootScope.previousState.params, {location: 'replace'});
        } else {
          window.history.back(-1);
        }
      }
    });
  }
})();