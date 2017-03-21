(function(){
  'use strict';
  angular.module('BlurAdmin.pages.errors')
      .directive('errorsList', errorsList);

  /** @ngInject */
  function errorsList(){
    return {
      restrict:    'E',
      controller:  'ErrorsListCtrl',
      templateUrl: 'app/pages/errors/errorsList/errorsList.html'
    };
  }
})();