/**
 * @author v.lugovksy
 * created on 16.12.2015
 */
(function () {
  'use strict';

  angular.module('BlurAdmin.pages.home')
      .directive('homeDashboard', homeDashboard);

  /** @ngInject */
  function homeDashboard() {
    return {
      restrict: 'E',
      controller: 'HomeDashboardCtrl',
      templateUrl: 'app/pages/home/homeDashboard/homeDashboard.html'
    };
  }
})();