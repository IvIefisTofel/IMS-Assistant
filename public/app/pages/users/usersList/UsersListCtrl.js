(function () {
  'use strict';
  angular.module('BlurAdmin.pages.users')
      .controller('UsersListCtrl', UsersListCtrl);

  /** @ngInject */
  function UsersListCtrl($scope, $rootScope) {
      if (!$rootScope.$getPermissions()) {
          window.history.back(-1);
      }
  }
})();