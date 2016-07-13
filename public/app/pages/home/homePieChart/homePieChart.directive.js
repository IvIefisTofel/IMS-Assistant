/**
 * @author v.lugovksy
 * created on 16.12.2015
 */
(function () {
  'use strict';

  angular.module('BlurAdmin.pages.home')
      .directive('homePieChart', homePieChart);

  /** @ngInject */
  function homePieChart() {
    return {
      restrict: 'E',
      controller: 'HomePieChartCtrl',
      templateUrl: 'app/pages/home/homePieChart/homePieChart.html'
    };
  }
})();