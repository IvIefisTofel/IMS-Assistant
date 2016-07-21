/**
 * @author v.lugovksy
 * created on 16.12.2015
 */
(function () {
  'use strict';

  angular.module('BlurAdmin.pages.home')
      .directive('homeParseJson', homeParseJson);

  /** @ngInject */
  function homeParseJson() {
    return {
      restrict: 'E',
      controller: 'HomeParseJsonCtrl',
      templateUrl: 'app/pages/home/homeParseJson/homeParseJson.html'
    };
  }
})();