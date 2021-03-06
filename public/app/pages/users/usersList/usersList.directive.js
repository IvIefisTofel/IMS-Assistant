(function(){
  'use strict';
  angular.module('BlurAdmin.pages.users')
      .directive('usersList', usersList);

  /** @ngInject */
  function usersList(){
    return {
      restrict:    'E',
      controller:  'UsersListCtrl',
      templateUrl: 'app/pages/users/usersList/usersList.html'
    };
  }
})();